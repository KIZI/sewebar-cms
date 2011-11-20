using System;
using System.Web;
using System.Xml.Linq;
using System.Xml.XPath;
using LMWrapper;
using LMWrapper.LISpMiner;
using log4net;

namespace SewebarWeb
{
	public class Task : API.HttpHandlerSession
	{
		private static readonly ILog Log = LogManager.GetLogger(typeof(Task));

	    private API.TaskResponse Response { get; set; }

        private API.TaskRequest Request { get; set; }

		protected string GetStatus(string xmlPath)
		{
			var document = XDocument.Load(xmlPath);

		    return document.XPathEvaluate("//@taskState") as string;
		}

		public override void ProcessRequest(HttpContext context)
		{
			base.ProcessRequest(context);

            this.Request = new API.TaskRequest(this.Miner, context);

            this.Response = new API.TaskResponse(context);

			if (this.Miner != null && this.Request.Task != null)
			{
				var status = "Not generated";

				var exporter = this.Miner.Exporter;
				exporter.Output = String.Format("{0}/results_{1}_{2:yyyyMMdd-Hmmss}.xml", this.Request.DataFolder, this.Request.TaskFileName, DateTime.Now);
				exporter.Template = String.Format(@"{0}\Sewebar\Template\ARDExport.LM.Template.txt", exporter.LMPath);
				//exporter.Template = String.Format(@"{0}\Sewebar\Template\4ftMiner.Task.PMML.Template.txt", exporter.LMPath);
				exporter.Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.ARD.txt", exporter.LMPath);
                exporter.TaskName = this.Request.TaskName;

				try
				{
                    // try to export results
					exporter.Execute();
					status = this.GetStatus(exporter.Output);
				}
				catch (LISpMinerException ex)
				{
                    // task was never imported - does not exists. Therefore we need to import at first.
					Log.Debug(ex);

					// import task
					var importer = this.Miner.Importer;
					importer.Input = this.Request.TaskName;
					importer.Alias = String.Format(@"{0}\Sewebar\Template\LM.PMML.Alias.ARD.txt", importer.LMPath);
					importer.Execute();
				}

				switch (status)
				{
					// * Not Generated (po zadání úlohy nebo změně v zadání)
					case "Not generated":
					// * Interrupted (přerušena -- buď kvůli time-outu nebo max počtu hypotéz)
					case "Interrupted":
						// run task - generate results
						if (this.Miner.Task4FtGen.Status == ExecutableStatus.Ready)
						{
							var task4FtGen = this.Miner.Task4FtGen;
							task4FtGen.TaskName = this.Request.TaskName;
							task4FtGen.Execute();

							// run export once again to refresh results and status
							if (status != "Interrupted")
								exporter.Execute();
						}
						else
						{
							Log.Debug("Waiting for result generation");
						}
						break;
					// * Running (běží generování)
					case "Running":
					// * Waiting (čeká na spuštění -- pro TaskPooler, zatím neimplementováno)
					case "Waiting":
						this.Miner.Task4FtGen.KeepAlive = 10;
						break;
					// * Solved (úspěšně dokončena)
					case "Solved":
					case "Finnished":
					default:
						break;
				}

			    this.Response.OutputFilePath = exporter.Output;

                this.Response.Write();
			}
		}
	}
}