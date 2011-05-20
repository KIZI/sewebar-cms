using System;
using System.Diagnostics;
using System.Text;

namespace LMWrapper.LISpMiner
{
	/// <summary>
	/// Imports PMML file into the metabase (transformation dictionary, tasks) 
	/// </summary>
	public class LMSwbImporter
	{
		/// <summary>
		/// /DSN:[data-source-name] ... data source name of metabase (if the data source name contains spaces, the whole /DSN paramater has to be enclosed in quatations mark, e.g. "/DSN:LM Barbora MB")
		/// </summary>
		public string Dsn { get; set; }

		/// <summary>
		/// /Input:[pmml_file] ... input PMML file with definitions
		/// </summary>
		public string Input { get; set; }

		/// <summary>
		/// /Quiet ... errors reported to _AppLog.dat instead on screen
		/// </summary>
		public bool Quiet { get; set; }

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

				// /Input:<pmml_file>
				if (!String.IsNullOrEmpty(this.Input))
				{
					arguments.AppendFormat("\"/Input:{0}\" ", this.Input);
				}

				// /Quiet
				if (this.Quiet)
				{
					arguments.Append("/Quiet ");
				}

				return arguments.ToString().Trim();
			}
		}

		public void Import()
		{
			// TODO: log4net
			Console.WriteLine(this.Arguments);
			
			var psi = new ProcessStartInfo(String.Format("{0}/LMSwbImporter.exe", this.LMPath))
			{
				Arguments = this.Arguments
			};

			var p = new Process { StartInfo = psi };
			p.Start();
			p.WaitForExit();
		}
	}
}