using System;
using System.Diagnostics;

namespace LMWrapper.LISpMiner
{
    public abstract class LMPooler : Executable, ITaskLauncher
    {
        private readonly Stopwatch _stopwatch;

        /// <summary>
        /// /TaskID [TaskID]		... TaskID of selected task
        /// </summary>
        public string TaskId { get; set; }

        /// <summary>
        /// /TaskName:[TaskName]		... Task.Name of the selected task
        /// </summary>
        public string TaskName { get; set; }

        /// <summary>
        /// /TimeOut [sec]			... optional: time-out in seconds (approx.) after generation (excluding initialisation) is automatically interrupted
        /// </summary>
        public int? TimeOut { get; set; }

        /// <summary>
        /// /ShutdownDelaySec:<n>		... (O) number of seconds <0;86400> before the LM TaskPooler server is shutted down after currently the last waiting task is solved (default: 10)
        /// </summary>
        public int? ShutdownDelaySec { get; set; }

        /// <summary>
        /// /TaskCancel			... (O) to cancel task of given TaskID or name (if already running) or to remove it from queue
        /// </summary>
        public bool TaskCancel { get; set; }

        /// <summary>
        /// /CancelAll			... (O) to cancel any running task and to empty the queue
        /// </summary>
        public bool CancelAll { get; set; }

        /// <summary>
        /// /TimeLog:<název_souboru>
        /// </summary>
        public abstract string TimeLog { get; }

        protected LMPooler()
        {
            this._stopwatch = new Stopwatch();
        }

        protected override void Run()
        {
            var info = new ProcessStartInfo
            {
                FileName = String.Format("{0}/{1}", this.LMExecutablesPath, this.ApplicationName),
                Arguments = this.Arguments
            };

            if (this.CancelAll || this.TaskCancel)
            {
                var stopwatch = new Stopwatch();

                using (var process = Process.Start(info))
                {
                    ExecutableLog.Debug(String.Format("Launching Task cancelation: {0} {1}", this.ApplicationName, this.Arguments));

                    stopwatch.Start();
                    process.WaitForExit();
                    stopwatch.Stop();

                    ExecutableLog.DebugFormat("Task cancelation finished in {2} ms: {0} {1}", this.ApplicationName, this.Arguments, stopwatch.Elapsed);
                }
            }
            else
            {
                var process = new Process
                {
                    EnableRaisingEvents = true,
                    StartInfo = info
                };

                process.Exited += (o, args) =>
                {
                    // we most probably already exited - there was an issue that this function was called twice
                    // using WaitForExit we are mixing synchronous and asynchronous way of process termination notification
                    if (this.Status == ExecutableStatus.Ready)
                    {
                        return;
                    }

                    this._stopwatch.Stop();

                    this.Status = ExecutableStatus.Ready;
                    ExecutableLog.DebugFormat("Result generation finished in {2} ms: {0} {1}", this.ApplicationName, this.Arguments, this._stopwatch.Elapsed);

                    process.Close();

                    this._stopwatch.Reset();
                };

                this.Status = ExecutableStatus.Running;
                ExecutableLog.Debug(String.Format("Launching: {0} {1}", this.ApplicationName, this.Arguments));

                this._stopwatch.Start();
                process.Start();

                // wait a little
                process.WaitForExit(5 * 1000);
            }
        }
    }
}