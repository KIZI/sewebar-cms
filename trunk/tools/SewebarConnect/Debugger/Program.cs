using System;

namespace Debugger
{
	class Program
	{
		static void Main(string[] args)
		{
			var env = new LMWrapper.Environment
			{
				LMPath = String.Format("{0}/../{1}", System.AppDomain.CurrentDomain.BaseDirectory, "LISp Miner"),
			};

			var exporter = new LMWrapper.LMSwbExporter
			               	{
			               		Environment = env,
			               		Dsn = "LM LM Barbora.mdb MB",
			               		MatrixName = "Loans",
			               		Output = String.Format("{0}/barboraDD.xml", AppDomain.CurrentDomain.BaseDirectory),
			               		Template = String.Format(@"{0}\Sewebar\Template\LMDataSource.Matrix.PMML.Template.txt", env.LMPath),
			               		Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.txt", env.LMPath),
			               		NoProgress = true,
								Quiet = true
			               	};

			exporter.Export();

			Console.WriteLine("Done.");

			Console.Read();
		}
	}
}
