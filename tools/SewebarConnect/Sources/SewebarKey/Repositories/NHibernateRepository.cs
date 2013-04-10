using System.Collections.Generic;
using System.Linq;
using NHibernate;
using NHibernate.Linq;

namespace SewebarKey.Repositories
{
	public class NHibernateRepository : IRepository
	{
		private readonly ISession _session;

		private ISession Session
		{
			get { return _session; }
		}

		public NHibernateRepository(ISession session)
		{
			this._session = session;
		}

		public TEntity Get<TEntity>(object id)
		{
			return Session.Get<TEntity>(id);
		}

		public IEnumerable<TEntity> FindAll<TEntity>() where TEntity : class
		{
			return Session
				.QueryOver<TEntity>()
				.List<TEntity>();
		}

		public IQueryable<TEntity> Query<TEntity>() where TEntity : class
		{
			return Session.Query<TEntity>();
		}

		public void Add(object entity)
		{
			Session.Persist(entity);
		}

		public void Save(object entity)
		{
			Session.SaveOrUpdate(entity);
		}

		public void Remove(object entity)
		{
			Session.Delete(entity);
		}
	}
}