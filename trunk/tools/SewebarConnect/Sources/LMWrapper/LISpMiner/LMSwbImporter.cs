using System;
using System.Text;

namespace LMWrapper.LISpMiner
{
	/// <summary>
	/// Imports PMML file into the metabase (transformation dictionary, tasks) 
	/// </summary>
	public class LMSwbImporter : Executable
	{
		/// <summary>
		/// /Alias:[alias_file]  ... aliases for text strings (for PMML mainly)
		/// </summary>
		public string Alias { get; set; }

		/// <summary>
		/// /Input:[pmml_file] ... input PMML file with definitions
		/// </summary>
		public string Input { get; set; }

		/// <summary>
		/// /TimeLog:<název_souboru>
		/// </summary>
		public string TimeLog
		{
			get
			{
				if (this.LISpMiner.Environment.TimeLog)
				{
					return String.Format("{0}/{1}.dat", this.LISpMiner.LMPrivatePath, "_TimeLog_LMSwbImporter");
				}

				return null;
			}
		}

		/// <summary>
		/// /NoCheckPrimaryKeyUnique	... (O) skip test of unique values in the database table primary key
		/// </summary>
		public bool NoCheckPrimaryKeyUnique { get; set; }

		public override string Arguments
		{
			get
			{
				var arguments = new StringBuilder("");

				if (!String.IsNullOrEmpty(this.OdbcConnectionString))
				{
					arguments.AppendFormat("/ODBCConnectionString=\"{0}\" ", this.OdbcConnectionString);
				}

				// /Input:<pmml_file>
				if (!String.IsNullOrEmpty(this.Input))
				{
					arguments.AppendFormat("\"/Input:{0}\" ", this.Input);
				}

				// /Alias:<alias_file>
				if (!String.IsNullOrEmpty(this.Alias))
				{
					arguments.AppendFormat("\"/Alias:{0}\" ", this.Alias);
				}

				// /NoCheckPrimaryKeyUnique
				if (this.NoCheckPrimaryKeyUnique)
				{
					arguments.Append("/NoCheckPrimaryKeyUnique ");
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

		internal LMSwbImporter(LISpMiner lispMiner, ODBC.ConnectionString connectionString, string lmPath)
			: base()
		{
			this.LISpMiner = lispMiner;
			this.LMPath = lmPath ?? this.LISpMiner.LMPath;
			this.OdbcConnectionString = connectionString.Value;

			this.ApplicationName = "LMSwbImporter.exe";
			this.AppLog = String.Format("{0}-{1}.dat", "_AppLog_importer", Guid.NewGuid());
		}
	}
}