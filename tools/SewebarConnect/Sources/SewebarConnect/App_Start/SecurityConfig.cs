using System.Linq;
using System.Web.Http;
using NHibernate;
using SewebarConnect.Security;
using SewebarKey;
using SewebarKey.Repositories;

namespace SewebarConnect
{
	public class SecurityConfig
	{
		public static void ConfigureGlobal(HttpConfiguration globalConfig)
		{
			globalConfig.MessageHandlers.Add(new AuthenticationHandler(CreateConfiguration()));
			globalConfig.Filters.Add(new SecurityExceptionFilter());
		}

		private static AuthenticationConfiguration CreateConfiguration()
		{
			var config = new AuthenticationConfiguration
			{
				DefaultAuthenticationScheme = "Basic",
			};

			config.AddBasicAuthentication(OnValidationDelegate);

			return config;
		}

		private static User OnValidationDelegate(string userName, string password)
		{
			ISession session = MvcApplication.SessionFactory.OpenSession();
			var repo = new NHibernateRepository(session);
			User user = repo.Query<SewebarKey.User>().FirstOrDefault(u => u.Username == userName && u.Password == password);

			session.Close();

			return user;
		}
	}
}