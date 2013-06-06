using System;
using System.IO;
using System.Text.RegularExpressions;
using System.Web;
using SewebarConnect.Controllers;

namespace SewebarConnect.API.Requests.Task
{
	public class TaskInfoRequest : Request
	{
		private static readonly string InvalidChars = String.Format(@"[{0}]+", Regex.Escape(new string(Path.GetInvalidFileNameChars())));

		private string _taskFileName;
		private string _taskPath;
	    private string _taskName = null;

		private string Task
		{
			get
			{
				return this.HttpContext.Request["task"];
			}
		}

		public string TaskName
		{
			get { return this._taskName ?? this.Task; }
			set { this._taskName = value; }
		}

		public string TaskFileName
		{
			get
			{
				if (String.IsNullOrEmpty(this._taskFileName))
				{
					this._taskFileName = Regex.Replace(this.TaskName, InvalidChars, "_");
				}

				return _taskFileName;
			}
		}

		public string TaskPath
		{
			get
			{
				if (String.IsNullOrEmpty(this._taskPath))
				{
					this._taskPath = String.Format("{0}/task_{1}_{2:yyyyMMdd-Hmmss}.xml",
												   this.DataFolder,
												   this.TaskFileName,
												   DateTime.Now);
				}

				if(!File.Exists(this._taskPath))
				{
					// save importing task XML
					using (var file = new StreamWriter(this._taskPath))
					{
						file.Write(this.Task);
					}
				}

				return this._taskPath;
			}
		}

		public TaskInfoRequest(BaseController controller)
			: base(controller.LISpMiner, controller.HttpContext)
		{
		}

		public TaskInfoRequest(ApiBaseController controller)
			: base(controller.LISpMiner, new HttpContextWrapper(System.Web.HttpContext.Current))
		{
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

		public string Alias
		{
			get
			{
				return this.HttpContext.Request["alias"];
			}
		}
	}
}