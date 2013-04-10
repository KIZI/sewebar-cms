using System.Collections.Generic;
using System.Linq;

namespace SewebarKey.Repositories
{
	public interface IRepository
	{
		TEntity Get<TEntity>(object id);
		
		IEnumerable<TEntity> FindAll<TEntity>() where TEntity : class;
		
		IQueryable<TEntity> Query<TEntity>() where TEntity : class;
		
		void Add(object entity);

		void Save(object entity);
		
		void Remove(object entity);
	}
}
