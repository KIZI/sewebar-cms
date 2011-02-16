using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using Sewebar;

namespace SewebarPublisher
{
    /// <summary>
    /// The main form class of the SEWEBAR Publisher component. 
    /// It contains only the non-generated code. 
    /// </summary>
    public partial class SEWEBARForm : Form
    {
        #region Fields

        /// <summary>
        /// A static array of strings containing the possible values of XML-RPC
        /// hosts including the URL of the XMLRPC server side service,
        /// full path to the service (including the directories)
        /// </summary>
        static string[] hosts = new string[5] { 
            "http://sewebar.vse.cz/xmlrpc/",
            "http://sewebar.vse.cz/cardio/xmlrpc/",
            "http://sewebar.vse.cz/adamek/xmlrpc/",
            "http://sewebar-dev.vse.cz/xmlrpc/", 
            "http://sewebar.vse.cz/tinnitus/xmlrpc/"
        };

        /// <summary>
        /// Defines ways to upload a file
        /// </summary>
        static string[] uploadWays = new string[2]
        {
            "Create a new article",
            "Update article (select from list)"
        };

        #endregion

        #region Constructor

        /// <summary>
        /// The constructor
        /// </summary>
        public SEWEBARForm()
        {
            InitializeComponent();

            //adding the hosts and selecting the index
            foreach (string s in hosts)
            {
                CBXMLRPCHost.Items.Add(s);
            }
            CBXMLRPCHost.SelectedIndex = 0;

            //adding upload ways
            foreach (string s in uploadWays)
            {
                CBChoose.Items.Add(s);
            }
            CBChoose.SelectedIndex = 0;

            //adding the name and password of the trial student (can be removed)
            //TBUserName.Text = "admin";
            //TBPassword.Text = "studentFIS";

            LVArticles.Columns.Add("Article ID", -2, HorizontalAlignment.Left);
            LVArticles.Columns.Add("Article title", -2, HorizontalAlignment.Left);

            this.ResumeLayout(false);
            this.PerformLayout();
        }

        /// <summary>
        /// The constructor that fills the task name for the user
        /// </summary>
        /// <param name="taskName">
        /// Name of the task (comming from the command line argument)
        /// </param>
        public SEWEBARForm(string taskName) : this()
        {
            TBNewArticle.Text = taskName;
        }

        #endregion

        #region Events

        /// <summary>
        /// Lists the files of the user
        /// </summary>
        /// <param name="sender">Sender of the event</param>
        /// <param name="e">Event arguments</param>
        private void BListFiles_Click(object sender, EventArgs e)
        {
            ListFiles();
        }

        /// <summary>
        /// The event closes the form
        /// </summary>
        /// <param name="sender">Sender of the event</param>
        /// <param name="e">Event arguments</param>
        private void BCancel_Click(object sender, EventArgs e)
        {
            this.Close();
        }

        /// <summary>
        /// The event publishes the content of the clipboard to SEWEBAR
        /// </summary>
        /// <param name="sender">Sender of the event</param>
        /// <param name="e">Event arguments</param>
        private void BPublish_Click(object sender, EventArgs e)
        {
            if (!Clipboard.ContainsText())
            {
                MessageBox.Show("The system clipboard does not contain text","Error", MessageBoxButtons.OK, MessageBoxIcon.Exclamation );
                return;
            }
            string pmml = Clipboard.GetText();

            string articleTitle;
            int articleID = -1;
            if (CBChoose.SelectedIndex == 0) //Create a new article
            {
                if (TBNewArticle.Text == string.Empty)
                {
                    MessageBox.Show("New article needs to have a name", "Error", MessageBoxButtons.OK, MessageBoxIcon.Exclamation);
                    return;
                }

                articleTitle = TBNewArticle.Text;
            }
            else // Select article from list
            {
                if (LVArticles.Items.Count == 0)
                {
                    MessageBox.Show("No article is selected", "Error", MessageBoxButtons.OK, MessageBoxIcon.Exclamation);
                    return;
                }
                if (LVArticles.SelectedItems.Count == 0)
                {
                    MessageBox.Show("No article is selected", "Error", MessageBoxButtons.OK, MessageBoxIcon.Exclamation);
                    return;
                }

                articleID = Convert.ToInt32(LVArticles.SelectedItems[0].Text);
                articleTitle = LVArticles.SelectedItems[0].SubItems[1].Text;
            }

            //retrieving the URL of the Joomla server
            string url;
            if (CBXMLRPCHost.SelectedIndex == -1)
            {
                url = CBXMLRPCHost.Text;
            }
            else
            {
                url = CBXMLRPCHost.SelectedItem.ToString();
            }

            string response = null;
            try
            {
                response = Sewebar.Sewebar.PublishToSewebar(
                    url,
                    pmml,
                    TBUserName.Text,
                    TBPassword.Text,
                    articleTitle,
                    articleID);
            }
            catch (Exception ex)
            {
                MessageBox.Show("File upload unsuccessfull\n" + ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Exclamation);
                return;
            }
            MessageBox.Show(response, "Response");

            if (!CHBDoNotClose.Checked)
            {
                this.Close();
            }
            else
            {
                ListFiles();
            }
        }

        /// <summary>
        /// The event disables or enables the listbox and textbox to choose the text from
        /// </summary>
        /// <param name="sender">Sender of the event</param>
        /// <param name="e">Event arguments</param>
        private void CBChoose_SelectedIndexChanged(object sender, EventArgs e)
        {
            if (CBChoose.SelectedIndex == 0)
            {
                LVArticles.Enabled = false;
                TBNewArticle.Enabled = true;
            }
            else
            {
                LVArticles.Enabled = true;
                TBNewArticle.Enabled = false;
            }
        }

        #endregion

        #region Protected methods

        /// <summary>
        /// The method lists files of a particular user and fills them into 
        /// the list view.
        /// </summary>
        protected void ListFiles()
        {
            LVArticles.Items.Clear();

            string url;
            if (CBXMLRPCHost.SelectedIndex == -1)
            {
                url = CBXMLRPCHost.Text;
            }
            else
            {
                url = CBXMLRPCHost.SelectedItem.ToString();
            }

            try
            {
                IDictionary<int, string> files =
                    Sewebar.Sewebar.ListFiles(
                        url,
                        TBUserName.Text, TBPassword.Text);

                foreach (int key in files.Keys)
                {
                    ListViewItem item = new ListViewItem(key.ToString());
                    item.SubItems.Add(files[key]);
                    LVArticles.Items.Add(item);
                    LVArticles.AutoResizeColumns(ColumnHeaderAutoResizeStyle.ColumnContent);
                }
            }
            catch (Exception ex)
            {
                MessageBox.Show(ex.Message, "Error", MessageBoxButtons.OK, MessageBoxIcon.Exclamation);
                return;
            }
        }

        #endregion
    }
}
