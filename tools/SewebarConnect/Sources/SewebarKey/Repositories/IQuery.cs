using System.Collections.Generic;
using NHibernate;

namespace SewebarKey.Repositories
{
    public interface IQuery<out TResult>
    {
        IEnumerable<TResult> Execute(ISession session);
    }
}