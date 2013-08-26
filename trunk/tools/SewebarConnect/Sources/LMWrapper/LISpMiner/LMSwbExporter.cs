using System;
using System.Text;

namespace LMWrapper.LISpMiner
{
	/// <summary>
	/// Exports the metabase data (task, results...) into a text file (PMML, XML, HTML) using template.
	/// </summary>
	public class LMSwbExporter : Executable
	{
		/// <summary>
		/// /MatrixID:[MatrixID]  ... MatrixID of the selected matrix
		/// </summary>
		public string MatrixId { get; set; }

		/// <summary>
		/// /MatrixName:[MatrixName] ... Matrix.Name of the selected matrix
		/// </summary>
		public string MatrixName { get; set; }

		/// <summary>
		/// /TaskID:[TaskID]  ... TaskID of the selected task
		/// </summary>
		public string TaskId { get; set; }

		/// <summary>
		/// /TaskName:[TaskName]  ... Task.Name of the selected task
		/// </summary>
		public string TaskName { get; set; }

		/// <summary>
		/// /Template:[template_file] ... path and name to the template file
		/// </summary>
		public string Template { get; set; }

		/// <summary>
		/// /Alias:[alias_file]  ... aliases for text strings (for PMML mainly)
		/// </summary>
		public string Alias { get; set; }

		/// <summary>
		/// /Output:[output_file]  ... path and name to the output file
		/// </summary>
		public string Output { get; set; }

		/// <summary>
		/// /Version    ... special mode to export system version
		/// </summary>
		public bool Version { get; set; }

		/// <summary>
		/// /Survey		... special mode to export survey of tasks
		/// </summary>
		public bool Survey { get; set; }

		/// <summary>
		/// /DistinctValuesMax:[nnn] ... maximal number of exported distinct values of DB columns (default: 1000)
		/// </summary>
		public string DistinctValuesMax { get; set; }

		/// <summary>
		/// /NoAttributeDisctinctValues ... (O) to skip reading distinct values of attributes from metabase (if not needed afterwards for export)
		/// </summary>
		public bool NoAttributeDisctinctValues { get; set; }

		/// <summary>
		/// /NoEscapeSeqUnicode ... (O) to suppress substitution of national characters for UNICODE escape-sequences (like: &Aacute, &Rcaron etc.) (default: FALSE)
		/// </summary>
		public bool NoEscapeSeqUnicode { get; set; }

		/// <summary>
		/// /TimeLog:<název_souboru>
		/// </summary>
		public string TimeLog
		{
			get
			{
				if (this.LISpMiner.Environment.TimeLog)
				{
					return String.Format("{0}/{1}.dat", this.LISpMiner.LMPrivatePath, "_TimeLog_LMSwbExporter");
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

				// /MatrixID:<MatrixID>
				if (!String.IsNullOrEmpty(this.MatrixId))
				{
					arguments.AppendFormat("\"/MatrixID:{0}\" ", this.MatrixId);
				}

				// /MatrixName:<MatrixName>
				if (!String.IsNullOrEmpty(this.MatrixName))
				{
					arguments.AppendFormat("\"/MatrixName:{0}\" ", this.MatrixName);
				}

				// /TaskID:<TaskID>
				if (!String.IsNullOrEmpty(this.TaskId))
				{
					arguments.AppendFormat("\"/TaskID:{0}\" ", this.TaskId);
				}

				// /TaskName:<TaskName>
				if (!String.IsNullOrEmpty(this.TaskName))
				{
					arguments.AppendFormat("\"/TaskName:{0}\" ", this.TaskName);
				}

				// /Template:<template_file>
				if (!String.IsNullOrEmpty(this.Template))
				{
					arguments.AppendFormat("\"/Template:{0}\" ", this.Template);
				}

				// /Alias:<alias_file>
				if (!String.IsNullOrEmpty(this.Alias))
				{
					arguments.AppendFormat("\"/Alias:{0}\" ", this.Alias);
				}

				// /Output:<output_file>
				if (!String.IsNullOrEmpty(this.Output))
				{
					arguments.AppendFormat("\"/Output:{0}\" ", this.Output);
				}

				// /DistinctValuesMax:<nnn>
				if (!String.IsNullOrEmpty(this.DistinctValuesMax))
				{
					arguments.AppendFormat("\"/DistinctValuesMax:{0}\" ", this.DistinctValuesMax);
				}

				if (this.NoAttributeDisctinctValues)
				{
					arguments.Append("/NoAttributeDisctinctValues ");
				}

				if (this.NoEscapeSeqUnicode)
				{
					arguments.Append("/NoEscapeSeqUnicode ");
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

				// /Version
				if (this.Version)
				{
					arguments.Append("/Version ");
				}

				// /Survey
				if (this.Survey)
				{
					arguments.Append("/Survey ");
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

		internal LMSwbExporter(LISpMiner lispMiner, ODBC.ConnectionString connectionString, string lmPath)
			: base()
		{
			this.LISpMiner = lispMiner;
			this.LMPath = lmPath ?? this.LISpMiner.LMPath;
			this.OdbcConnectionString = connectionString.Value;

			this.ApplicationName = "LMSwbExporter.exe";
			this.AppLog = String.Format("{0}-{1}.dat", "_AppLog_exporter", Guid.NewGuid());

			this.NoAttributeDisctinctValues = false;
			this.NoEscapeSeqUnicode = false;
		}

		/// <summary>
		/// Either the TaskID or task name have to be specifed for export the whole task. 
		/// The TaskID has preference if both are specifed.
		///
		/// Either the MatrixID or matrix name have to be specified for export of the
		/// DataDictionary. The MatrixID has preference if both are specifed.
		/// </summary>
		protected override void Run()
		{
			base.Run();
		}
	}
}
