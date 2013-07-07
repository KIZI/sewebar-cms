using System;
using System.Text;

namespace LMWrapper.LISpMiner
{
	public class LMProcPooler : Executable, ITaskLauncher
	{
		// /RMCaseID:<RMCaseID>		... ReverseMiner CaseID to run all tasks in this case	
		// /ShutdownDelaySec:<n>		... (O) number of seconds <0;86400> before the LM TaskPooler server is shutted down after currently the last waiting task is solved (default: 10)
		// /TimeMaxHours:<n>		... (O) maximal number of hours the server is running (to allow for periodical re-start) (default: 1)
		// /Server				... (O) this instance becomes the server (the Task parameter is ignored)

		/// <summary>
		/// /TaskID [TaskID]		... TaskID of selected task
		/// </summary>
		public string TaskId { get; set; }

		/// <summary>
		/// /TaskName:[TaskName]		... Task.Name of the selected task
		/// </summary>
		public string TaskName { get; set; }

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
		/// /TimeOut:[sec]			... (O) time-out in seconds (approx.) after a task generation (excluding initialisation) is automatically interrupted
		/// </summary>
		public int? TimeOut { get; set; }

		public override string Arguments
		{
			get
			{
				var arguments = new StringBuilder("");

				if (!String.IsNullOrEmpty(this.OdbcConnectionString))
				{
					arguments.AppendFormat("/ODBCConnectionString=\"{0}\" ", this.OdbcConnectionString);
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

				// /TaskCancel
				if (this.TaskCancel)
				{
					arguments.Append("/TaskCancel ");
				}

				// /CancelAll
				if (this.CancelAll)
				{
					arguments.Append("/CancelAll ");
				}

				// /TimeOut <sec>
				if (this.TimeOut != null)
				{
					arguments.AppendFormat("/TimeOut:{0} ", this.TimeOut);
				}

				// /ShutdownDelaySec:<n>
				if (this.ShutdownDelaySec != null)
				{
					arguments.AppendFormat("/ShutdownDelaySec:{0} ", this.ShutdownDelaySec);
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

		internal LMProcPooler(LISpMiner lispMiner, ODBC.ConnectionString connectionString, string lmPath)
			: base()
		{
			this.LISpMiner = lispMiner;
			this.LMPath = lmPath ?? this.LISpMiner.LMPath;
			this.OdbcConnectionString = connectionString.Value;

			this.ApplicationName = "LMProcPooler.exe";
			this.AppLog = String.Format("{0}-{1}.dat", "_AppLog_LMProcPooler", Guid.NewGuid());
			this.CancelAll = false;
			// this.TimeOut = 10;
		}
	}
}
