using System;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Web.Http;
using LMWrapper.LISpMiner;
using SewebarConnect.API;
using SewebarKey.Repositories;

namespace SewebarConnect.Controllers
{
	public class ApiBaseController : ApiController
	{
		protected const string PARAMS_GUID = "minerId";

		private LISpMiner _miner;
		private IRepository _repository;

		protected virtual IRepository Repository
		{
			get { return _repository ?? (_repository = new NHibernateRepository(MvcApplication.SessionFactory.GetCurrentSession())); }
		}

		public LISpMiner LISpMiner
		{
			get
			{
				if (this._miner == null)
				{
					if (this.ControllerContext.RouteData.Values[PARAMS_GUID] == null)
					{
						// we need a context
						throw new Exception(string.Format("{0} is required parameter in this context", PARAMS_GUID));
					}

					var guid = this.ControllerContext.RouteData.Values[PARAMS_GUID] as string;

					if (guid == null)
					{
						throw new Exception(String.Format("Not specified which LISpMiner to work with."));
					}

					if (!MvcApplication.Environment.Exists(guid))
					{
						throw new Exception(String.Format("Requested LISpMiner with ID {0}, does not exists", guid));
					}

					this._miner = MvcApplication.Environment.GetMiner(guid);
				}

				return this._miner;
			}
		}

		/// <summary>
		/// Searches database for authorized Sewebar.User.
		/// </summary>
		/// <returns>Authorized Sewebar.User.</returns>
		protected SewebarKey.User GetSewebarUser()
		{
			if (this.User == null)
			{
				return null;
			}

			return this.Repository.Query<SewebarKey.User>()
				.FirstOrDefault(u => u.Username == (this.User.Identity.Name) /* && u.Password == password */);
		}

		protected T ThrowHttpReponseException<T>(string message = null, HttpStatusCode code = HttpStatusCode.InternalServerError)
		{
			if (!string.IsNullOrEmpty(message))
			{
				var body = new Response(message)
					{
						Status = Status.Failure
					};

				var reponse = ControllerContext.Request.CreateResponse(code, body);

				throw new HttpResponseException(reponse);
			}

			throw new HttpResponseException(code);
		}

		protected Response ThrowHttpReponseException(string message = null, HttpStatusCode code = HttpStatusCode.InternalServerError)
		{
			return this.ThrowHttpReponseException<Response>(message, code);
		}

		protected T ThrowHttpReponseException<T>(HttpStatusCode code = HttpStatusCode.InternalServerError)
		{
			return this.ThrowHttpReponseException<T>(null, code);
		}
	}
}