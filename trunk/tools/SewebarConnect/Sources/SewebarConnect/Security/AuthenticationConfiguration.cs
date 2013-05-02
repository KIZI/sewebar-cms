using System;
using System.Collections.Generic;
using System.Linq;
using System.IdentityModel.Tokens;
using System.Security.Claims;

namespace SewebarConnect.Security
{
	public class AuthenticationOptionMapping
	{
		public AuthenticationOptions Options { get; set; }
		public SecurityTokenHandlerCollection TokenHandler { get; set; }
	}

	public enum HttpRequestType
	{
		Header,
		AuthorizationHeader,
		QueryString,
		ClientCertificate,
		Cookie
	}

	public class AuthenticationConfiguration
	{
		private bool _hasAuthorizationHeader;
		private bool _hasHeader;
		private bool _hasQueryString;
		private bool _hasCookie;
		private bool _hasClientCert;

		public List<AuthenticationOptionMapping> Mappings { get; set; }

		public ClaimsAuthenticationManager ClaimsAuthenticationManager { get; set; }

		public string DefaultAuthenticationScheme { get; set; }

		public bool HasAuthorizationHeaderMapping
		{
			get { return _hasAuthorizationHeader; }
		}

		public bool HasHeaderMapping
		{
			get { return _hasHeader; }
		}

		public bool HasQueryStringMapping
		{
			get { return _hasQueryString; }
		}

		public bool HasCookieMapping
		{
			get { return _hasCookie; }
		}

		public bool HasClientCertificateMapping
		{
			get { return _hasClientCert; }
		}

		public AuthenticationConfiguration()
		{
			Mappings = new List<AuthenticationOptionMapping>();
			DefaultAuthenticationScheme = "unspecified";
		}

		public void AddBasicAuthentication(BasicAuthenticationSecurityTokenHandler.ValidateUserNameCredentialDelegate validationDelegate)
		{
			var handler = new BasicAuthenticationSecurityTokenHandler(validationDelegate);

			AddMapping(new AuthenticationOptionMapping
			{
				TokenHandler = new SecurityTokenHandlerCollection { handler },
				Options = AuthenticationOptions.ForAuthorizationHeader(scheme: "Basic")
			});
		}

		private void AddMapping(AuthenticationOptionMapping mapping)
		{
			var hit = from m in Mappings
					  where m.Options.RequestType == mapping.Options.RequestType &&
							m.Options.Name == mapping.Options.Name &&
							m.Options.Scheme == mapping.Options.Scheme
					  select m;

			if (hit.FirstOrDefault() != null)
			{
				throw new InvalidOperationException("Duplicate authentication entry");
			}

			Mappings.Add(mapping);

			switch (mapping.Options.RequestType)
			{
				case HttpRequestType.AuthorizationHeader:
					_hasAuthorizationHeader = true;
					break;
				case HttpRequestType.Header:
					_hasHeader = true;
					break;
				case HttpRequestType.QueryString:
					_hasQueryString = true;
					break;
				case HttpRequestType.Cookie:
					_hasCookie = true;
					break;
				case HttpRequestType.ClientCertificate:
					_hasClientCert = true;
					break;
				default:
					throw new InvalidOperationException("Invalid request type");
			}
		}

		#region Mapping retrieval

		public bool TryGetAuthorizationHeaderMapping(string scheme, out SecurityTokenHandlerCollection handler)
		{
			handler = (from m in Mappings
					   where m.Options.RequestType == HttpRequestType.AuthorizationHeader &&
							 m.Options.Name == "Authorization" &&
							 m.Options.Scheme == scheme
					   select m.TokenHandler).SingleOrDefault();

			return (handler != null);
		}

		public bool TryGetHeaderMapping(string headerName, out SecurityTokenHandlerCollection handler)
		{
			handler = (from m in Mappings
					   where m.Options.RequestType == HttpRequestType.Header &&
							 m.Options.Name == headerName
					   select m.TokenHandler).SingleOrDefault();

			return (handler != null);
		}

		public bool TryGetQueryStringMapping(string paramName, out SecurityTokenHandlerCollection handler)
		{
			handler = (from m in Mappings
					   where m.Options.RequestType == HttpRequestType.QueryString &&
							 m.Options.Name == paramName
					   select m.TokenHandler).SingleOrDefault();

			return (handler != null);
		}

		public bool TryGetClientCertificateMapping(out SecurityTokenHandlerCollection handler)
		{
			handler = (from m in Mappings
					   where m.Options.RequestType == HttpRequestType.ClientCertificate
					   select m.TokenHandler).SingleOrDefault();

			return (handler != null);
		}

		#endregion
	}
}