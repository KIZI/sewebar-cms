﻿using System;
using System.Threading;
using System.Threading.Tasks;
using System.Web.Http.Controllers;
using System.Web.Http.Filters;
using NHibernate;

namespace SewebarConnect.Filters
{
	[AttributeUsage(AttributeTargets.Method, AllowMultiple = true)]
	public class NHibernateTransactionAttribute : NHibernateSessionAttribute, IExceptionFilter
	{
		private struct Void
		{
		}

		private readonly Task _completedTask = Task.FromResult<Void>(new Void());

		protected ISession Session
		{
			get { return SessionFactory.GetCurrentSession(); }
		}

		public override void OnActionExecuting(HttpActionContext filterContext)
		{
			base.OnActionExecuting(filterContext);

			Session.BeginTransaction();
		}

		public override void OnActionExecuted(HttpActionExecutedContext actionExecutedContext)
		{
			ITransaction tx = Session.Transaction;

			if (tx != null && tx.IsActive)
			{
				Session.Transaction.Commit();
			}

			base.OnActionExecuted(actionExecutedContext);
		}

		public Task ExecuteExceptionFilterAsync(HttpActionExecutedContext actionExecutedContext, CancellationToken cancellationToken)
		{
			try
			{
				ITransaction tx = Session.Transaction;

				if (tx != null && tx.IsActive)
				{
					Session.Transaction.Rollback();
				}
			}
			catch
			{
				// possibly no session...
			}

			return _completedTask;
		}
	}
}