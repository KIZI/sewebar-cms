using System;
using System.Collections;
using System.Net;
using System.Xml.Linq;
using NUnit.Framework;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace SewebarWebTests
{
    [TestFixture]
    public class WebApiTests
    {
        private const string LMcloudServer = "http://localhost/SewebarConnect";

    	private static WebClient _client;

        private CookieAwareWebClient _sessionsClient;

        protected string Download(string url)
        {
			if(_client == null)
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
			catch(Exception exception)
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

                var jObject = JObject.Parse(data);

                Assert.AreEqual("success", (string) jObject["Status"], String.Format("Registration failed - {0}", jObject["Reason"]));
            }
            catch(Exception exception)
            {
                Assert.Fail(exception.Message);
            }
		}

		#endregion
	}
}
