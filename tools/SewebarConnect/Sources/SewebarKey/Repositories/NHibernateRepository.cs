using System;
using System.Collections.Generic;
using System.Linq;
using NHibernate;

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

        public IEnumerable<TEntity> Find<TEntity>(IQuery<TEntity> query) where TEntity : class
        {
            return query.Execute(Session);
        }

        public TEntity FindFirst<TEntity>(IQuery<TEntity> query) where TEntity : class
        {
            return One(Find(query), true);
        }

        public TEntity FindFirstOrDefault<TEntity>(IQuery<TEntity> query) where TEntity : class
        {
            return One(Find(query), false);
        }

        public void Execute(ICommand command)
        {
            command.Execute(Session);
        }

        public void Add(object entity)
        {
            Session.Persist(entity);
        }

        public void Remove(object entity)
        {
            Session.Delete(entity);
        }

        private T One<T>(IEnumerable<T> items, bool throwIfNone)
        {
            var itemsList = items.ToList();

            if (throwIfNone && itemsList.Count == 0)
            {
                throw new Exception(string.Format("Expected at least one '{0}' in the query results", typeof(T).Name));
            }
            
			return itemsList.Count > 0
                       ? itemsList[0]
                       : default(T);
        }
    }
}