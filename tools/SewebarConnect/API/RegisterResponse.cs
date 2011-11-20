using System.Web;
using Newtonsoft.Json;

namespace SewebarWeb.API
{
    public class RegisterResponse : Response
    {
        public string Name { get; set; }

        public RegisterResponse(HttpContext context) : base(context)
        {
        }

        public override void Write()
        {
            //this.HttpContext.Response.ContentType = "application/json";
            this.HttpContext.Response.ContentType = "text/plain";

            var result = new { Status = "success", Name = this.Name };
            this.HttpContext.Response.Write(JsonConvert.SerializeObject(result));
        }
    }
}