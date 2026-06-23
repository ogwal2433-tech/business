@extends('layouts.app')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
  .messages-container {
    max-height: 75vh;
    overflow-y: auto;
  }
  textarea.form-control {
    resize: vertical;
  }
</style>
@endsection

@section('content')
<div class="container mt-5">
  <h2 class="mb-4"><i class="bi bi-inbox-fill"></i> Employee Messages</h2>

  <div class="mb-4">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by user or subject..." onkeyup="filterMessages()">
  </div>

  <div class="messages-container">
    @forelse ($messages as $message)
    <div class="card mb-3 shadow-sm message-card">
      <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
          <strong>{{ $message->user->name }} <small class="text-muted">&lt;{{ $message->user->email }}&gt;</small></strong><br>
          <small class="text-muted">Sent on: {{ $message->created_at->format('M d, Y H:i') }}</small>
        </div>
        <div class="d-flex align-items-center gap-3">
          <span class="badge
            {{ $message->status === 'responded' ? 'bg-success' : ($message->status === 'read' ? 'bg-info' : 'bg-secondary') }}">
            {{ ucfirst($message->status) }}
          </span>

          <div class="dropdown">
            <button class="btn btn-sm btn-outline-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-trash"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
       <form action="{{ route('messages.delete', $message->id) }}" method="POST" onsubmit="return confirm('Delete for everyone? This cannot be undone.')">
  @csrf
  @method('DELETE')
  <input type="hidden" name="scope" value="everyone" />
  <button type="submit" class="dropdown-item text-danger">Delete for Everyone</button>
</form>

<form action="{{ route('messages.delete', $message->id) }}" method="POST" onsubmit="return confirm('Delete for me?')">
  @csrf
  @method('DELETE')
  <input type="hidden" name="scope" value="me" />
  <button type="submit" class="dropdown-item">Delete for Me Only</button>
</form>

              </li>
            </ul>
          </div>
        </div>
      </div>

      <div class="card-body">
        <p><strong>Subject:</strong> <span class="message-subject">{{ $message->subject }}</span></p>
        <p class="message-text">{{ $message->message }}</p>

        @if ($message->admin_reply)
          <div class="alert alert-success mt-3">
            <strong>Your Reply:</strong> {{ $message->admin_reply }}
          </div>
        @else
          <form method="POST" action="{{ route('admin.messages.reply', $message->id) }}">
            @csrf
            <div class="mb-2">
              <textarea name="admin_reply" class="form-control" rows="3" placeholder="Write your reply..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-reply-fill"></i> Send Reply
            </button>
          </form>
        @endif
      </div>
    </div>
  @empty
<div class="text-center my-5">
  <i class="bi bi-chat-dots" style="font-size: 4rem; color: #6c757d;"></i>
  <p class="mt-3 fs-5 text-muted">No messages found.</p>
  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#messageEmployeeModal">
    <i class="bi bi-envelope-fill"></i> Message an Employee
  </button>
</div>

<!-- Modal -->
<div class="modal fade" id="messageEmployeeModal" tabindex="-1" aria-labelledby="messageEmployeeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('admin.messages.send') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="messageEmployeeModalLabel">Send Message to Employee</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="employee_id" class="form-label">Select Employee</label>
          <select name="employee_id" id="employee_id" class="form-select" required>
            <option value="" disabled selected>Select an employee</option>
          @foreach(App\Models\User::where('role', 'employee')->where('admin_id', auth()->id())->get() as $employee)
  <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->email }})</option>
@endforeach

          </select>
        </div>
        <div class="mb-3">
          <label for="message" class="form-label">Message</label>
          <textarea name="message" id="message" rows="4" class="form-control" placeholder="Type your message..." required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Send Message</button>
      </div>
    </form>
  </div>
</div>
@endempty

  </div>
</div>

<script>
  function filterMessages() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.message-card');

    cards.forEach(card => {
      const userName = card.querySelector('strong').innerText.toLowerCase();
      const subject = card.querySelector('.message-subject').innerText.toLowerCase();
      card.style.display = userName.includes(input) || subject.includes(input) ? '' : 'none';
    });
  }

  // Real-time polling for new messages
  document.addEventListener('DOMContentLoaded', function() {
    var container = document.querySelector('.messages-container');
    var lastCheck = new Date().toISOString();

    function pollMessages() {
      fetch('/api/messages/new?since=' + encodeURIComponent(lastCheck))
        .then(function(r) { return r.json(); })
        .then(function(data) {
          if (data.messages && data.messages.length > 0) {
            lastCheck = new Date().toISOString();
            var emptyState = container.querySelector('.text-center.my-5');
            if (emptyState) emptyState.remove();

            data.messages.forEach(function(m) {
              var card = document.createElement('div');
              card.className = 'card mb-3 shadow-sm message-card animate-fade-in';
              card.innerHTML =
                '<div class="card-header bg-light d-flex justify-content-between align-items-center">' +
                  '<div><strong>' + m.user + '</strong><br><small class="text-muted">' + m.time_ago + '</small></div>' +
                  '<span class="badge bg-info">New</span>' +
                '</div>' +
                '<div class="card-body"><p class="message-text">' + m.message + '</p></div>';
              container.insertBefore(card, container.firstChild);
            });
          }
        })
        .catch(function() {});
    }

    setInterval(pollMessages, 5000);
  });
</script>

<style>
  @keyframes fadeInMsg { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:translateY(0); } }
  .animate-fade-in { animation: fadeInMsg 0.3s ease-out; }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
