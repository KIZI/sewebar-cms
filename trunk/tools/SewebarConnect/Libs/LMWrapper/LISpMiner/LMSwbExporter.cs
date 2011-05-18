using System;
using System.Diagnostics;
using System.Text;

namespace LMWrapper.LISpMiner
{
	/// <summary>
	/// Exports the metabase data (task, results...) into a text file (PMML, XML, HTML) using template.
	/// </summary>
	public class LMSwbExporter
	{
		/// <summary>
		/// /DSN:[data-source-name]  ... data source name of metabase (if the data source name contains spaces, the whole /DSN paramater has to be enclosed in quatations mark, e.g. "/DSN:LM Barbora MB")
		/// </summary>
		public string Dsn { get; set; }

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
		/// /DistinctValuesMax:[nnn] ... maximal number of exported distinct values of DB columns (default: 1000)
		/// </summary>
		public string DistinctValuesMax { get; set; }
		
		/// <summary>
		/// /Quiet    ... errors reported to _AppLog.dat instead on screen
		/// </summary>
		public bool Quiet { get; set; }

		/// <summary>
		/// /NoProgress   ... no progress dialog is displayed
		/// </summary>
		public bool NoProgress { get; set; }

		public string LMPath { get; set; }

		public string Arguments
		{
			get
			{
				var arguments = new StringBuilder("");

				if (!String.IsNullOrEmpty(this.Dsn))
				{
					arguments.AppendFormat("\"/DSN:{0}\" ", this.Dsn);
				}

				// /MatrixID:<MatrixID>
				if (!String.IsNullOrEmpty(this.MatrixId))
				{
					arguments.AppendFormat("\"/MatrixID:{0}\" ", this.Dsn);
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

				return arguments.ToString().Trim();
			}
		}

		/// <summary>
		/// Either the TaskID or task name have to be specifed for export the whole task. 
		/// The TaskID has preference if both are specifed.
		///
		/// Either the MatrixID or matrix name have to be specified for export of the
		/// DataDictionary. The MatrixID has preference if both are specifed.
		/// </summary>
		public void Export()
		{
			Console.WriteLine(this.Arguments);

			var psi = new ProcessStartInfo(String.Format("{0}/LMSwbExporter.exe", this.LMPath))
			          	{
			          		Arguments = this.Arguments,
							
			          	};

			var p = new Process {StartInfo = psi};
			p.Start();
			p.WaitForExit();
		}
	}
}
