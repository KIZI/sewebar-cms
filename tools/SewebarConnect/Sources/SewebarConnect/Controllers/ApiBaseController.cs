using System;
using System.Web.Http;
using LMWrapper.LISpMiner;

namespace SewebarConnect.Controllers
{
	public class ApiBaseController : ApiController
	{
		protected const string PARAMS_GUID = "minerId";

		private LISpMiner _miner;

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
	}
}