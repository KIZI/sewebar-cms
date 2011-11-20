using System.IO;
using System.Web;

namespace SewebarWeb.API
{
	public class TaskResponse : Response
	{
		public string OutputFilePath { get; set; }

		public TaskResponse(HttpContext context)
			: base(context)
		{
		}

		public override void Write()
		{
			this.HttpContext.Response.ContentType = "text/xml";

			if (this.Status == Status.failure || !File.Exists(this.OutputFilePath))
			{
				this.Status = Status.failure;
				this.Message = this.Message ?? "Results generation did not succeed.";

				this.WriteException();
				return;
			}

			// write results to response
			this.HttpContext.Response.WriteFile(this.OutputFilePath);
		}
	}
}