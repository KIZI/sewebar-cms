using System.Web.Http;
using SewebarConnect.Security;

namespace SewebarConnect
{
    public class SecurityConfig
    {
        public static void ConfigureGlobal(HttpConfiguration globalConfig)
        {
            System.Diagnostics.Debugger.Break();

            globalConfig.MessageHandlers.Add(new AuthenticationHandler(CreateConfiguration()));
            globalConfig.Filters.Add(new SecurityExceptionFilter());
        }

        private static AuthenticationConfiguration CreateConfiguration()
        {
            var config = new AuthenticationConfiguration
            {
                DefaultAuthenticationScheme = "Basic",
            };

            config.AddBasicAuthentication((userName, password) => userName == password);

            return config;
        }
    }
}