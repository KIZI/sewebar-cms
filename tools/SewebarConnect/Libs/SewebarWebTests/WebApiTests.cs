using System;
using System.Net;
using NUnit.Framework;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace SewebarWebTests
{
    [TestFixture]
    public class WebApiTests
    {
        private const string lmcloudServer = "http://localhost/SewebarConnect";

        protected string Download(string url)
        {
            return new WebClient().DownloadString(url);
        }

        [Test]
        public void DefaultTest()
        {
            try
            {
                Assert.IsNotNullOrEmpty(Download(string.Format("{0}/Default.aspx", lmcloudServer)));
            }
            catch(Exception exception)
            {
                Assert.Fail(exception.Message);
            }
        }

        [Test]
        public void RegisterMySQLDatabase()
        {
            try
            {
                string data = Download(
                        string.Format(
                            @"{0}/Register.ashx?type=mysqlconnection&server={1}&database={2}&username={3}&password={4}",
                            lmcloudServer, "localhost", "lisp", "lisp", "lisp"));

                var jObject = JObject.Parse(data);

                Assert.AreEqual("success", (string) jObject["Status"], String.Format("Registration failed - {0}", jObject["Reason"]));
            }
            catch(Exception exception)
            {
                Assert.Fail(exception.Message);
            }
        }
    }
}
