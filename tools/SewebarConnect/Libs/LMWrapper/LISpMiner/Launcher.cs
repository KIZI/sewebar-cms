using System;
using System.IO;

namespace LMWrapper.LISpMiner
{
	public abstract class Launcher
	{
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

		protected Launcher()
		{
			this.Quiet = true;
			this.NoProgress = true;
		}

		public void Launch()
		{
			var errorFilePath = String.Format("{0}/_AppLog.dat", this.LMPath);
			
			// TODO: log4net
			Console.WriteLine(this.Arguments);
			
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
		}
	}
}
