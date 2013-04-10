﻿using System.Web.Mvc;
using SewebarConnect.API;
using log4net;

namespace SewebarConnect.Controllers.TaskGen
{
	public class GridPoolController : TaskGenBaseController
	{
		private static readonly ILog log = LogManager.GetLogger(typeof (GridPoolController));

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
					                    Launcher = this.LISpMiner.LMGridPooler
				                    });
		}

		[ValidateInput(false)]
		[ErrorHandler]
		public ActionResult Cancel(string taskName)
		{
			return this.CancelTask(new TaskDefinition
				                       {
					                       Launcher = this.LISpMiner.LMGridPooler,
					                       TaskName = taskName
				                       });
		}
	}
}