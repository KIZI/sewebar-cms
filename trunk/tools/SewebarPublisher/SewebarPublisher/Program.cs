using System;
using System.Collections.Generic;
using System.Windows.Forms;
using System.Reflection;
using System.IO;

namespace SewebarPublisher
{
    static class Program
    {
        /// <summary>
        /// The main entry point for the application.
        /// </summary>
        [STAThread]
        static void Main(string[] args)
        {
            Application.EnableVisualStyles();
            Application.SetCompatibleTextRenderingDefault(false);
            
            if (LibrariesPresent())
            {
                if (args != null && args.Length > 0)
                {
                    Application.Run(new SEWEBARForm(args[0]));
                }
                else
                {
                    Application.Run(new SEWEBARForm());
                }
            }
        }

        /// <summary>
        /// Checks, if the libriaries with supporting SEWERAB and XMLRPC functions are present. 
        /// </summary>
        static Boolean LibrariesPresent()
        {
            Assembly mainAssembly = Assembly.GetExecutingAssembly();

            string fullLocation = mainAssembly.Location;
            string name = mainAssembly.ManifestModule.Name;

            int location = fullLocation.IndexOf(name);
            string directory = fullLocation.Substring(0, location);

            string sewebar = directory + "Sewebar.dll";
            string cook = directory + "CookComputing.XmlRpcV2.dll";

            if (!File.Exists(sewebar) || !File.Exists(cook))
            {
                MessageBox.Show("The libraries Sewebar.dll or CookComputing.XmlRpcV2.dll do not exist in the directory with executable", 
                                "Error", MessageBoxButtons.OK, MessageBoxIcon.Exclamation);
                return false;
            }
            else
            {
                return true;
            }
        }
    }
}
