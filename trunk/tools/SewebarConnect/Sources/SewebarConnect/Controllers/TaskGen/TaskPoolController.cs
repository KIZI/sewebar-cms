using System.Web.Mvc;
using SewebarConnect.API;
using log4net;

namespace SewebarConnect.Controllers.TaskGen
{
	public class TaskPoolController : TaskGenBaseController
	{
		private static readonly ILog log = LogManager.GetLogger(typeof(TaskPoolController));

		protected override ILog Log
		{
			get { return log; }
		}

		[ValidateInput(false)]
		[ErrorHandler]
		public ActionResult Run()
		{
			return this.RunTask(new TaskDefinition
				                    {
					                    DefaultTemplate = "ETreeMiner.Task.Template.PMML",
					                    Launcher = this.LISpMiner.LMTaskPooler
				                    });
		}

		[ValidateInput(false)]
		[ErrorHandler]
		public ActionResult Cancel(string taskName)
		{
			return this.CancelTask(new TaskDefinition
				                    {
					                    Launcher = this.LISpMiner.LMTaskPooler,
										TaskName = taskName
				                    });
		}
	}
}
