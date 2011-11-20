using System;
using System.Collections.Specialized;
using System.Web;
using LMWrapper.LISpMiner;
using LMWrapper.ODBC;

namespace SewebarWeb.API
{
    public class RegisterRequest : Request
    {
        private string _type;
        private OdbcDrivers _connection;

        #region Properties

        public OdbcDrivers Connection
        {
            get
            {
                if (String.IsNullOrEmpty(this._type))
                {
                    OdbcDrivers connection;
                    this._type = this.HttpContext.Request.Params["type"];

                    if (Enum.TryParse(this._type, true, out connection))
                    {
                        this._connection = connection;
                    }
                    else
                    {
                        throw new Exception("Database was not correctly defined (type).");
                    }
                }

                return this._connection;
            }
        }

        public NameValueCollection Parameters
        {
            get { return this.HttpContext.Request.Params; }
        }

        #endregion

        public RegisterRequest(LISpMiner miner, HttpContext context)
            : base(miner, context)
        {
        }
    }
}