using System.Linq;
using System.Net;
using System.Web.Http;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Users;
using SewebarConnect.API.Responses.Users;

namespace SewebarConnect.Controllers
{
	[APIErrorHandler]
	[Authorize]
	public class DatabasesController : ApiBaseController
	{
		/// <summary>
		/// Tries to find database by it identification.
		/// </summary>
		/// <param name="username">User identification.</param>
		/// <param name="id">Database identification.</param>
		/// <returns>DatabaseResponse.</returns>
		[Filters.NHibernateTransaction]
		public DatabaseResponse Get(string username, string id)
		{
			if (this.User.Identity.Name == username || this.User.IsInRole("admin"))
			{
				var owner = this.Repository.Query<SewebarKey.User>().FirstOrDefault(u => u.Username == username);

				if (owner != null)
				{
					SewebarKey.Database database = owner.Databases.FirstOrDefault(d => d.Name == id);

					if (database != null)
					{
						return new DatabaseResponse(database);
					}
				}

				return ThrowHttpReponseException<DatabaseResponse>(
					string.Format("User \"{0}\" was not found therefore can't find his database.", username),
					HttpStatusCode.NotFound);
			}

			return ThrowHttpReponseException<DatabaseResponse>(HttpStatusCode.Unauthorized);
		}

		/// <summary>
		/// Register database for existing user.
		/// </summary>
		/// <returns>Created database.</returns>
		[Filters.NHibernateTransaction]
		public DatabaseResponse Post(string username)
		{
			var request = new UserRequest(this);
			var user = this.GetSewebarUser();
			var database = request.GetDatabase(user);

			if (database != null)
			{
				user.Databases.Add(database);

				this.Repository.Save(database);

				return new DatabaseResponse(database);
			}

			return ThrowHttpReponseException<DatabaseResponse>(
				"No database to register",
				HttpStatusCode.BadRequest);
		}

		/// <summary>
		/// Updates password for given database.
		/// </summary>
		/// <param name="username">Owner's username.</param>
		/// <param name="id">Database identification.</param>
		/// <returns>Upated database.</returns>
		[Filters.NHibernateTransaction]
		public DatabaseResponse Put(string username, string id)
		{
			var request = new UserRequest(this);
			
			if (this.User.Identity.Name == username || this.User.IsInRole("admin"))
			{
				SewebarKey.Database database = this.Repository.Query<SewebarKey.Database>()
									.FirstOrDefault(d => d.Name == id && d.Owner.Username == username);

				if (database != null)
				{
					database.Password = request.DbPassword;

					this.Repository.Save(database);

					return new DatabaseResponse(database);
				}

				return ThrowHttpReponseException<DatabaseResponse>(
					string.Format("Database \"{0}\" for user \"{1}\" was not found.", id, username),
					HttpStatusCode.NotFound);
			}

			return ThrowHttpReponseException<DatabaseResponse>(
				string.Format("Database \"{0}\" was not found or you are not authorized to modify it.", id),
				HttpStatusCode.Unauthorized);
		}

		/// <summary>
		/// Removes database.
		/// </summary>
		/// <param name="username">Owner's username.</param>
		/// <param name="id">Database identification.</param>
		/// <returns>Response.</returns>
		[Filters.NHibernateTransaction]
		public Response Delete(string username, string id)
		{
			if (this.User.Identity.Name == username || this.User.IsInRole("admin"))
			{
				SewebarKey.Database database = this.Repository.Query<SewebarKey.Database>()
									.FirstOrDefault(d => d.Name == id && d.Owner.Username == username);

				if (database != null)
				{
					this.Repository.Remove(database);

					return new Response(string.Format("Database {0} removed.", id));
				}

				return ThrowHttpReponseException<DatabaseResponse>(
					string.Format("Database \"{0}\" for user \"{1}\" was not found.", id, username),
					HttpStatusCode.NotFound);
			}

			return ThrowHttpReponseException<DatabaseResponse>(
				string.Format("Database \"{0}\" was not found or you are not authorized to remove it.", id),
				HttpStatusCode.Unauthorized);
		}
 	}
}
