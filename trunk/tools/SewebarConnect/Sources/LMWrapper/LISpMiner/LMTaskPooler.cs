using System;
using System.Text;

namespace LMWrapper.LISpMiner
{
	public class LMTaskPooler : LMPooler
	{
		public override string TimeLog
		{
			get
			{
				if (this.LISpMiner.Environment.TimeLog)
				{
					return String.Format("{0}/{1}.dat", this.LISpMiner.LMPrivatePath, "_TimeLog_LMTaskPooler");
				}

				return null;
			}
		}

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
					arguments.AppendFormat("\"/AppLog:{0}\" ", this.AppLog);
				}

				// /TimeLog
				if (!String.IsNullOrEmpty(this.TimeLog))
				{
					arguments.AppendFormat("\"/TimeLog:{0}\"", this.TimeLog);
				}

				return arguments.ToString().Trim();
			}
		}

		internal LMTaskPooler(LISpMiner lispMiner, ODBC.ConnectionString connectionString, string lmPrivatePath)
			: base()
		{
			this.LISpMiner = lispMiner;
			this.LMExecutablesPath = this.LISpMiner.LMExecutablesPath;
			this.LMPrivatePath = lmPrivatePath;
			this.OdbcConnectionString = connectionString.Value;

			this.ApplicationName = "LMTaskPooler.exe";
			this.AppLog = String.Format("{0}-{1}.dat", "_AppLog_LMTaskPooler", Guid.NewGuid());
			//this.TimeOut = 10;

			this.CancelAll = false;
		}
	}
}
