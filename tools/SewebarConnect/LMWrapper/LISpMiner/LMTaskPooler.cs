using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.Text;
using log4net;

namespace LMWrapper.LISpMiner
{
	public class LMTaskPooler : Executable
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(LMTaskPooler));

		private Process _process;

		private readonly Stopwatch _stopwatch;

		protected Process Process
		{
			get
			{
				if (this._process == null)
				{
					this._process = new Process
					                	{
					                		EnableRaisingEvents = true,
					                		StartInfo = new ProcessStartInfo
					                		            	{
					                		            		FileName = String.Format("{0}/{1}", this.LMPath, this.ApplicationName),
					                		            		Arguments = this.Arguments
					                		            	},
					                	};

					this._process.Exited += new EventHandler(ProcessExited);
				}

				return this._process;
			}
		}

		/// <summary>
		/// /DSN:[data-source-name] ... data source name of metabase (if the data source name contains spaces, the whole /DSN paramater has to be enclosed in quatations mark, e.g. "/DSN:LM Barbora MB")
		/// </summary>
		public override string Dsn { get; set; }

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

		public int KeepAlive { get; set; }

		public override string Arguments
		{
			get
			{
				var arguments = new StringBuilder("");

				if (!String.IsNullOrEmpty(this.Dsn))
				{
					arguments.AppendFormat("/DSN {0} ", this.Dsn);
				}

				// /TaskID <TaskID>
				if (!String.IsNullOrEmpty(this.TaskId))
				{
					arguments.AppendFormat("/TaskID:{0} ", this.TaskId);
				}

				// /TaskName <TaskName>
				if (!String.IsNullOrEmpty(this.TaskName))
				{
					arguments.AppendFormat("\"/TaskName:{0}\" ", this.TaskName);
				}

				// /TimeOut <sec>
				if (this.TimeOut != null)
				{
					arguments.AppendFormat("/TimeOut {0} ", this.TimeOut);
				}

				// /Quiet
				if (this.Quiet)
				{
					arguments.Append("/Quiet ");
				}

				// /NoProgress
				if (this.NoProgress)
				{
					arguments.Append("/NoProgress ");
				}

				// /AppLog
				if (!String.IsNullOrEmpty(this.AppLog))
				{
					arguments.AppendFormat("\"/AppLog:{0}\"", this.AppLog);
				}

				return arguments.ToString().Trim();
			}
		}

		public LMTaskPooler()
			: base()
		{
			this.ApplicationName = "LMTaskPooler.exe";
			this._stopwatch = new Stopwatch();
			this.AppLog = String.Format("{0}-{1}.dat", "_AppLog_LMTaskPooler", Guid.NewGuid());
			//this.TimeOut = 10;
		}

		protected override void Run()
		{
			if (this.Status != ExecutableStatus.Ready) return;

			this.Status = ExecutableStatus.Running;

			Log.Debug(String.Format("Launching: {0} {1}", this.ApplicationName, this.Arguments));

			this._stopwatch.Start();

			this.Process.Start();
			// wait a little
			this.Process.WaitForExit(5*1000);
		}

		private void ProcessExited(object sender, EventArgs e)
		{
			this._stopwatch.Stop();
			this.Status = ExecutableStatus.Ready;

			Log.InfoFormat("Result generation finished in {2} ms: {0} {1}", this.ApplicationName, this.Arguments,
			               this._stopwatch.Elapsed);

			this.Process.Close();
			this._process = null;
		}

		public void Stop()
		{
			if (!this.Process.HasExited)
			{
				Log.Debug("stopping / killing");

				if (!this.Process.CloseMainWindow())
				{
					this.Stop();
				}
			}
		}
	}
}
