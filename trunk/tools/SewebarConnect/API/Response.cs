using System.Web;

namespace SewebarWeb.API
{
    public abstract class Response
    {
        public HttpContext HttpContext { get; private set; }

        public string Message { get; set; }

        public Status Status { get; set; }

        public Response(HttpContext context)
        {
            this.HttpContext = context;
        }

        public abstract void Write();
    }
}