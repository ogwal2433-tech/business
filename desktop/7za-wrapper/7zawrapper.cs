using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.IO;

class Program
{
    static int Main(string[] args)
    {
        var wrapperDir = Path.GetDirectoryName(Environment.GetCommandLineArgs()[0]);
        var original7za = Path.Combine(wrapperDir, "7z-real.exe");
        
        var newArgs = new List<string>();
        foreach (var arg in args)
        {
            if (arg == "-snld")
                newArgs.Add("-snl-");
            else
                newArgs.Add(arg);
        }
        
        var process = new Process();
        process.StartInfo.FileName = original7za;
        process.StartInfo.Arguments = string.Join(" ", newArgs);
        process.StartInfo.UseShellExecute = false;
        process.StartInfo.CreateNoWindow = true;
        process.Start();
        process.WaitForExit();
        
        return process.ExitCode == 2 ? 0 : process.ExitCode;
    }
}
