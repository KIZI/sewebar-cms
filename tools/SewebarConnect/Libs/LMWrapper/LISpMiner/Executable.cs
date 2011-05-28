using System;
using System.Diagnostics;
using System.IO;
using log4net;

namespace LMWrapper.LISpMiner
{
	public abstract class Executable
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(Executable));

		public LISpMiner LISpMiner { get; set; }

		public string ApplicationName { get; protected set; }

		public ExecutableStatus Status { get; protected set; }

		public virtual string Dsn { get; set; }

		public virtual string LMPath { get; set; }

		/// <summary>
		/// /Quiet    ... errors reported to _AppLog.dat instead on screen
		/// </summary>
		public bool Quiet { get; set; }

		/// <summary>
		/// /NoProgress   ... no progress dialog is displayed
		/// </summary>
		public bool NoProgress { get; set; }

		public abstract string Arguments { get; }

		protected Executable()
		{
			this.Quiet = true;
			this.NoProgress = true;
			this.Status = ExecutableStatus.Ready;
		}

		public void Execute()
		{
			var errorFilePath = String.Format("{0}/_AppLog.dat", this.LMPath);

			this.Run();

			if(File.Exists(errorFilePath))
			{
				var message = File.ReadAllText(errorFilePath);
				File.Delete(errorFilePath);
				throw new LISpMinerException(message);
			}
		}

		protected virtual void Run()
		{
			var p = new Process
			        	{
			        		StartInfo = new ProcessStartInfo
			        		            	{
												FileName = String.Format("{0}/{1}", this.LMPath, this.ApplicationName),
			        		            		Arguments = this.Arguments
			        		            	}
			        	};

			this.Status = ExecutableStatus.Running;
			
			Log.DebugFormat("Launching: {0} {1}", this.ApplicationName, this.Arguments);

			p.Start();
			p.WaitForExit();
			
			this.Status = ExecutableStatus.Ready;
		}
	}
}
