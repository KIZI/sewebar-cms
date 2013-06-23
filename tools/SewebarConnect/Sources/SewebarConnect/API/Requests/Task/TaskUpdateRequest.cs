using System.Web;
using System.Xml.Linq;
using SewebarConnect.Controllers;

namespace SewebarConnect.API.Requests.Task
{
    public class TaskUpdateRequest : Request
    {
        public ApiBaseController Controller { get; private set; }
        private XDocument _buffer;

        public TaskUpdateRequest(ApiBaseController controller)
			: base(controller.LISpMiner, new HttpContextWrapper(System.Web.HttpContext.Current))
        {
            Controller = controller;
        }

        internal Request GetRequestType()
        {
            var doc = this.GetRequest();

            if (doc.Root != null && doc.Root.Name == "CancelationRequest")
            {
                return new TaskCancelationRequest(this);
            }

            return null;
        }

        private XDocument GetRequest()
        {
            return _buffer ?? (_buffer = XDocument.Load(this.HttpContext.Request.InputStream));
        }
    }
}