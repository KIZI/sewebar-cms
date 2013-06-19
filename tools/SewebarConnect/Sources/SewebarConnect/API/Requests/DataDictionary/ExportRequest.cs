using System.Web;
using SewebarConnect.Controllers;

namespace SewebarConnect.API.Requests.DataDictionary
{
	public class ExportRequest : Request
	{
		public ExportRequest(ApiBaseController controller)
			: base(controller.LISpMiner, new HttpContextWrapper(System.Web.HttpContext.Current))
		{
		}

		public string MatrixName
		{
			get
			{
				var matrix = this.HttpContext.Request["matrix"];

				if (string.IsNullOrEmpty(matrix))
				{
					return "Loans";
				}

				return matrix;
			}
		}

		public string GetTemplate(string defaultValue)
		{
			var template = this.HttpContext.Request["template"];

			if (string.IsNullOrEmpty(template))
			{
				return defaultValue;
			}

			return template;
		}
	}
}