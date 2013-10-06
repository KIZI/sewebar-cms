using System;
using System.Linq;
using System.Net;
using System.Web.Http;
using LMWrapper;
using LMWrapper.LISpMiner;
using SewebarConnect.API;
using SewebarConnect.API.Requests.Application;
using SewebarConnect.API.Responses.Application;

namespace SewebarConnect.Controllers
{
	[Authorize]
	[APIErrorHandler]
	public class MinersController : ApiBaseController
	{
		private void CheckMinerOwnerShip()
		{
			var user = this.GetSewebarUser();
			var miner = this.Repository.Query<SewebarKey.Miner>()
				.FirstOrDefault(m => m.MinerId == this.LISpMiner.Id);

			if ((miner != null && user.Username != miner.Owner.Username) && !this.User.IsInRole("admin"))
			{
				this.ThrowHttpReponseException("Authorized user is not allowed to use this miner.", HttpStatusCode.Forbidden);
			}
		}

		[Filters.NHibernateTransaction]
		public LISpMinerResponse Get()
		{
			try
			{
				CheckMinerOwnerShip();

				var lm = this.LISpMiner;

				// one miner
				return new LISpMinerResponse(lm);
			}
			catch (Exception)
			{
				// all miners
				return new LISpMinerResponse();
			}
		}

		[AllowAnonymous]
		[Filters.NHibernateTransaction]
		public RegistrationResponse Post()
		{
			var request = new RegistrationRequest();
			var id = ShortGuid.NewGuid();
			var user = this.GetSewebarUser();
			var miner = new LISpMiner(MvcApplication.Environment, id.ToString(), request.DbConnection, request.Metabase, request.SharedBinaries);

			MvcApplication.Environment.Register(miner);

			// is user authenticated
			if (user == null)
			{
				var owner = request.Owner;
				
				if (owner != null)
				{
					// user sent in XML to be registered
					user = new SewebarKey.User
					{
						Username = owner.Username,
						Password = owner.Password
					};

					this.Repository.Add(user);
				}
				else
				{
					// use anon user
					user = this.GetAnonymousUser();
				}
			}

			user.Miners.Add(new SewebarKey.Miner
			{
				Owner = user,
				MinerId = miner.Id,
				Path = miner.LMPrivatePath
			});

			this.Repository.Save(user);

			return new RegistrationResponse { Id = id };
		}

		[Filters.NHibernateTransaction]
		public Response Delete()
		{
			CheckMinerOwnerShip();

			var miner = this.Repository.Query<SewebarKey.Miner>()
				.FirstOrDefault(m => m.MinerId == this.LISpMiner.Id);

			MvcApplication.Environment.Unregister(this.LISpMiner);

			if (miner != null)
			{
				miner.Owner.Miners.Remove(miner);

				this.Repository.Remove(miner);
			}

			return new Response {Status = Status.Success, Message = "LISpMiner removed."};
		}
	}
}
