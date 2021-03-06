using NHibernate;
using NHibernate.Cfg;

namespace SewebarKey.Configurations
{
	public interface ISessionManager
	{
		Configuration Configuration { get; }

		void CreateDatabase();

		void UpdateDatabase();

		ISessionFactory BuildSessionFactory();
	}
}