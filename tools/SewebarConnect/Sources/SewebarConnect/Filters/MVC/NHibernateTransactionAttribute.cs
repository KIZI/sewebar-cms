using System;
using System.Web.Mvc;
using NHibernate;
using NHibernate.Context;

namespace SewebarConnect.Filters.Mvc
{
	[AttributeUsage(AttributeTargets.Method, AllowMultiple = true)]
	public class NHibernateTransactionAttribute : ActionFilterAttribute, IExceptionFilter
	{
		protected ISessionFactory SessionFactory
		{
			get { return MvcApplication.SessionFactory; }
		}

		protected ISession Session
		{
			get { return SessionFactory.GetCurrentSession(); }
		}

		public override void OnActionExecuting(ActionExecutingContext filterContext)
		{
			ISession session = SessionFactory.OpenSession();
			CurrentSessionContext.Bind(session);

			Session.BeginTransaction();
		}

		public override void OnResultExecuted(ResultExecutedContext filterContext)
		{
			ITransaction tx = Session.Transaction;

			if (tx != null && tx.IsActive)
			{
				Session.Transaction.Commit();
			}

			ISession session = CurrentSessionContext.Unbind(SessionFactory);
			session.Close();
		}

		public void OnException(ExceptionContext filterContext)
		{
			try
			{
				ITransaction tx = Session.Transaction;

				if (tx != null && tx.IsActive)
				{
					Session.Transaction.Rollback();

					ISession session = CurrentSessionContext.Unbind(SessionFactory);
					session.Close();
				}
			}
			catch
			{
				// possibly no session...
			}
		}
	}
}