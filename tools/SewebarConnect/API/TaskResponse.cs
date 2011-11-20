using System.IO;
using System.Web;
using LMWrapper;

namespace SewebarWeb.API
{
    public class TaskResponse : Response
    {
        public string OutputFilePath { get; set; }

        public TaskResponse(HttpContext context): base(context)
        {
        }

        public override void Write()
        {
            this.HttpContext.Response.ContentType = "text/xml";

            // write results to response
            if (File.Exists(this.OutputFilePath))
            {
                this.HttpContext.Response.WriteFile(this.OutputFilePath);
                //context.Response.Write(String.Format("{0}", status));
            }
            else
            {
                throw new LISpMinerException("Results generation did not succeed.");
            }
        }
    }
}