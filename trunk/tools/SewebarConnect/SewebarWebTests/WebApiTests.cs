using System;
using System.IO;
using System.Linq;
using System.Net;
using System.Xml.Linq;
using NUnit.Framework;

namespace SewebarWebTests
{
	[TestFixture]
	public class WebApiTests
	{
		private const string LMcloudServer = "http://localhost/SewebarConnect";
		private const string TestCasesPath = "TestCases";

		private static WebClient _client;

		private CookieAwareWebClient _sessionsClient;

		protected string Download(string url)
		{
			if (_client == null)
			{
				_client = new WebClient();
			}

			return _client.DownloadString(url);
		}

		[SetUp]
		public void Init()
		{
			this._sessionsClient = new CookieAwareWebClient();
		}

		[TearDown]
		public void Cleanup()
		{

		}

		[Test]
		public void DefaultTest()
		{
			try
			{
				Assert.IsNotNullOrEmpty(Download(String.Format("{0}/Default.aspx", LMcloudServer)));
			}
			catch (Exception exception)
			{
				Assert.Fail(exception.Message);
			}
		}

		#region Session Tests

		[Test]
		public void ImportDataDictionarySession()
		{
			try
			{
				var response = _sessionsClient.DownloadString(String.Format("{0}/Import.ashx", LMcloudServer));

				var xml = XElement.Parse(response);

				Assert.IsNotNull(xml.Attribute("id"));
			}
			catch (Exception exception)
			{
				Assert.Fail(exception.Message);
			}
		}

		[Test]
		public void RunTaskSession()
		{
			try
			{
				using (var stream = new StreamReader(String.Format("{0}/ETReeMiner.Task52.xml", TestCasesPath)))
				{
					string parameters = String.Format("content={0}", System.Web.HttpUtility.UrlEncode(stream.ReadToEnd()));

					_sessionsClient.Headers[HttpRequestHeader.ContentType] = "application/x-www-form-urlencoded";
					string response = _sessionsClient.UploadString(String.Format("{0}/Task.ashx", LMcloudServer), parameters);
				}
			}
			catch (Exception exception)
			{
				Assert.Fail(exception.Message);
			}
		}

		#endregion

		#region Registration Tests

		[Test]
		public void RegisterMySQLDatabase()
		{
			try
			{
				string data = Download(
					String.Format(
						@"{0}/Register.ashx?type=mysqlconnection&server={1}&database={2}&username={3}&password={4}",
						LMcloudServer, "localhost", "lisp", "lisp", "lisp"));

				var doc = XDocument.Parse(data).Element("response");
				Assert.NotNull(doc);

				var status = doc.Attribute("status");
				Assert.NotNull(status);

				Assert.AreEqual("success", status.Value);

				Assert.NotNull(doc.Attribute("id"));
			}
			catch (Exception exception)
			{
				Assert.Fail(exception.Message);
			}
		}

		#endregion
	}
}
