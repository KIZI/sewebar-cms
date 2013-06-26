using System.Linq;
using System.Net;
using System.Web;
using System.Web.Http;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Users;
using SewebarConnect.API.Responses.Users;
using SewebarKey;

namespace SewebarConnect.Controllers
{
	/// <summary>
	/// TODO: make admin operations
	/// </summary>
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
			Database database = null;
			User user = this.GetSewebarUser();

			if (user != null)
			{
				database = user.Databases.FirstOrDefault(d => d.Name == id);
			}

			return new DatabaseResponse(database);
        }

		/// <summary>
		/// Register database for existing user.
		/// </summary>
		/// <returns>Created database.</returns>
		[Filters.NHibernateTransaction]
		public DatabaseResponse Post(string username)
		{
			var request = new UserRequest(this);
			User user = this.GetSewebarUser();
			Database database = request.GetDatabase(user);

			if (database != null)
			{
				user.Databases.Add(database);
			}

			this.Repository.Save(database);

			return new DatabaseResponse(database);
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
			User user = this.GetSewebarUser();
			Database data = request.GetDatabase(user);
			Database database = this.Repository.Query<Database>()
									.FirstOrDefault(d => d.Name == id && d.Owner.Username == username);

			if (database != null)
			{
				database.Password = data.Password;

				this.Repository.Save(database);

				return new DatabaseResponse(database);
			}

			throw new HttpResponseException(HttpStatusCode.NotFound);
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
			Database database = this.Repository.Query<Database>()
			                        .FirstOrDefault(d => d.Name == id && d.Owner.Username == username);

			if (database == null)
			{
				throw new HttpResponseException(HttpStatusCode.NotFound);
			}

			this.Repository.Remove(database);

			return new Response(string.Format("Database {0} removed.", id));
		}
 	}
}
