import './bootstrap';
// Check if the browser supports service workers
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function () {
    navigator.serviceWorker.register('/service-worker.js').then(function (registration) {
      console.log('Service Worker registered with scope:', registration.scope);
    }).catch(function (error) {
      console.log('Service Worker registration failed:', error);
    });
  });
}
let deferredPrompt;  // For storing the install prompt event
const installButton = document.getElementById('downloadApp');  // The button to trigger install

// Listen for the beforeinstallprompt event
window.addEventListener('beforeinstallprompt', (e) => {
  // Prevent the default installation prompt
  e.preventDefault();
  deferredPrompt = e;

  // Show the install button when the app is installable
  installButton.style.display = 'inline-flex';  // Make the button visible

  // When the user clicks the install button
  installButton.addEventListener('click', () => {
    // Show the native install prompt
    deferredPrompt.prompt();

    // Wait for the user's choice (install or dismiss)
    deferredPrompt.userChoice.then((choiceResult) => {
      if (choiceResult.outcome === 'accepted') {
        console.log('User accepted the install prompt');
      } else {
        console.log('User dismissed the install prompt');
      }
      deferredPrompt = null;  // Reset the prompt
    });
  });
});
