using System;
using System.Web;

namespace SewebarWeb.API
{
    public abstract class HttpHandler : IHttpHandler
    {
        private HttpContext _context;

        #region Properties

        public bool IsReusable
        {
            get
            {
                return false;
            }
        }

        protected HttpContext Context
        {
            get { return this._context; }
        }

        #endregion

        public virtual void ProcessRequest(HttpContext context)
        {
            this._context = context;
        }
    }
}