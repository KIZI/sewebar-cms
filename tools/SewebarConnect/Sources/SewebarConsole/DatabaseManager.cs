using System;
using System.Collections.Generic;
using System.Linq;
using NHibernate;
using SewebarKey;
using SewebarKey.Configurations;
using SewebarKey.Repositories;

namespace SewebarConsole
{
	internal class DatabaseManager
	{
		public ISessionManager SessionManager { get; private set; }

		public DatabaseManager()
		{
			SessionManager = new NHibernateSessionManager();
		}

		public DatabaseManager(string cfg)
		{
			SessionManager = new NHibernateSessionManager(cfg);
		}

		public void Create()
		{
			SessionManager.CreateDatabase();	
		}

		public void Init()
		{
			using (var session = SessionManager.BuildSessionFactory().OpenSession())
			{
				using (var tx = session.BeginTransaction())
				{
					IRepository repository = new NHibernateRepository(session);

					var admin = repository.Query<User>()
						.FirstOrDefault(u => u.Username == "admin") ?? new User();

					admin.Username = "admin";
					admin.Password = "sewebar";
					admin.Email = "andrej.hazucha@vse.cz";
					admin.Role = "admin";

					repository.Save(admin);

					var anon = repository.Query<User>()
						.FirstOrDefault(u => u.Username == "anonymous") ?? new User();

					anon.Username = "anonymous";
					anon.Password = "";
					anon.Role = "user";

					repository.Save(anon);

					tx.Commit();
				}

				session.Close();
			}
		}

		public void Update()
		{
			SessionManager.UpdateDatabase();	
		}

		public void Migrate(string cfg2)
		{
			var users = this.GetData();
			
			this.SaveData(cfg2, users);
		}

		private IEnumerable<User> GetData()
		{
			User[] users;

			using (var sourceSession = SessionManager.BuildSessionFactory().OpenSession())
			{
				IRepository repository = new NHibernateRepository(sourceSession);

				users = repository.Query<User>().ToArray();

				sourceSession.Close();
			}

			return users;
		}

		private void SaveData(string cfg, IEnumerable<User> users)
		{
			var targetSessionManager = new NHibernateSessionManager(cfg);

			targetSessionManager.CreateDatabase();

			using (var targetSession = targetSessionManager.BuildSessionFactory().OpenSession())
			{
				using (var tx = targetSession.BeginTransaction())
				{
					try
					{
						IRepository repository = new NHibernateRepository(targetSession);

						foreach (var user in users)
						{
							targetSession.Replicate(user, ReplicationMode.Exception);

							repository.Save(user);
						}

						tx.Commit();
					}
					catch (Exception)
					{
						tx.Rollback();

						throw;
					}
				}

				targetSession.Close();
			}
		}
	}
}
