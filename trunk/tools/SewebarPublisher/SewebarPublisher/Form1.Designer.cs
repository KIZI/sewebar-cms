namespace SewebarPublisher
{
    partial class SEWEBARForm
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(SEWEBARForm));
            this.label1 = new System.Windows.Forms.Label();
            this.CBXMLRPCHost = new System.Windows.Forms.ComboBox();
            this.TBUserName = new System.Windows.Forms.TextBox();
            this.label2 = new System.Windows.Forms.Label();
            this.label3 = new System.Windows.Forms.Label();
            this.TBPassword = new System.Windows.Forms.TextBox();
            this.LVArticles = new System.Windows.Forms.ListView();
            this.label4 = new System.Windows.Forms.Label();
            this.label5 = new System.Windows.Forms.Label();
            this.TBNewArticle = new System.Windows.Forms.TextBox();
            this.BPublish = new System.Windows.Forms.Button();
            this.BClose = new System.Windows.Forms.Button();
            this.BListFiles = new System.Windows.Forms.Button();
            this.CHBDoNotClose = new System.Windows.Forms.CheckBox();
            this.label6 = new System.Windows.Forms.Label();
            this.CBChoose = new System.Windows.Forms.ComboBox();
            this.SuspendLayout();
            // 
            // label1
            // 
            this.label1.AutoSize = true;
            this.label1.Location = new System.Drawing.Point(12, 9);
            this.label1.Name = "label1";
            this.label1.Size = new System.Drawing.Size(113, 13);
            this.label1.TabIndex = 0;
            this.label1.Text = "Select XML-RPC host:";
            // 
            // CBXMLRPCHost
            // 
            this.CBXMLRPCHost.FormattingEnabled = true;
            this.CBXMLRPCHost.Location = new System.Drawing.Point(15, 25);
            this.CBXMLRPCHost.Name = "CBXMLRPCHost";
            this.CBXMLRPCHost.Size = new System.Drawing.Size(202, 21);
            this.CBXMLRPCHost.TabIndex = 1;
            // 
            // TBUserName
            // 
            this.TBUserName.Location = new System.Drawing.Point(16, 65);
            this.TBUserName.Name = "TBUserName";
            this.TBUserName.Size = new System.Drawing.Size(201, 20);
            this.TBUserName.TabIndex = 2;
            // 
            // label2
            // 
            this.label2.AutoSize = true;
            this.label2.Location = new System.Drawing.Point(13, 49);
            this.label2.Name = "label2";
            this.label2.Size = new System.Drawing.Size(58, 13);
            this.label2.TabIndex = 3;
            this.label2.Text = "User name";
            // 
            // label3
            // 
            this.label3.AutoSize = true;
            this.label3.Location = new System.Drawing.Point(13, 88);
            this.label3.Name = "label3";
            this.label3.Size = new System.Drawing.Size(53, 13);
            this.label3.TabIndex = 4;
            this.label3.Text = "Password";
            // 
            // TBPassword
            // 
            this.TBPassword.Location = new System.Drawing.Point(15, 104);
            this.TBPassword.Name = "TBPassword";
            this.TBPassword.Size = new System.Drawing.Size(202, 20);
            this.TBPassword.TabIndex = 5;
            this.TBPassword.UseSystemPasswordChar = true;
            // 
            // LVArticles
            // 
            this.LVArticles.FullRowSelect = true;
            this.LVArticles.Location = new System.Drawing.Point(226, 25);
            this.LVArticles.MultiSelect = false;
            this.LVArticles.Name = "LVArticles";
            this.LVArticles.Size = new System.Drawing.Size(403, 123);
            this.LVArticles.TabIndex = 6;
            this.LVArticles.UseCompatibleStateImageBehavior = false;
            this.LVArticles.View = System.Windows.Forms.View.Details;
            // 
            // label4
            // 
            this.label4.AutoSize = true;
            this.label4.Location = new System.Drawing.Point(223, 9);
            this.label4.Name = "label4";
            this.label4.Size = new System.Drawing.Size(131, 13);
            this.label4.TabIndex = 7;
            this.label4.Text = "Select an article to update";
            // 
            // label5
            // 
            this.label5.AutoSize = true;
            this.label5.Location = new System.Drawing.Point(223, 151);
            this.label5.Name = "label5";
            this.label5.Size = new System.Drawing.Size(202, 13);
            this.label5.TabIndex = 8;
            this.label5.Text = "or write a name of a new article to publish";
            // 
            // TBNewArticle
            // 
            this.TBNewArticle.Location = new System.Drawing.Point(226, 172);
            this.TBNewArticle.Name = "TBNewArticle";
            this.TBNewArticle.Size = new System.Drawing.Size(403, 20);
            this.TBNewArticle.TabIndex = 9;
            // 
            // BPublish
            // 
            this.BPublish.Font = new System.Drawing.Font("Microsoft Sans Serif", 8.25F, System.Drawing.FontStyle.Bold, System.Drawing.GraphicsUnit.Point, ((byte)(238)));
            this.BPublish.Location = new System.Drawing.Point(226, 198);
            this.BPublish.Name = "BPublish";
            this.BPublish.Size = new System.Drawing.Size(322, 23);
            this.BPublish.TabIndex = 10;
            this.BPublish.Text = "Publish to SEWEBAR";
            this.BPublish.UseVisualStyleBackColor = true;
            this.BPublish.Click += new System.EventHandler(this.BPublish_Click);
            // 
            // BClose
            // 
            this.BClose.DialogResult = System.Windows.Forms.DialogResult.Cancel;
            this.BClose.Location = new System.Drawing.Point(554, 198);
            this.BClose.Name = "BClose";
            this.BClose.Size = new System.Drawing.Size(75, 23);
            this.BClose.TabIndex = 11;
            this.BClose.Text = "Close";
            this.BClose.UseVisualStyleBackColor = true;
            this.BClose.Click += new System.EventHandler(this.BCancel_Click);
            // 
            // BListFiles
            // 
            this.BListFiles.Location = new System.Drawing.Point(12, 198);
            this.BListFiles.Name = "BListFiles";
            this.BListFiles.Size = new System.Drawing.Size(203, 23);
            this.BListFiles.TabIndex = 12;
            this.BListFiles.Text = "List files of the user";
            this.BListFiles.UseVisualStyleBackColor = true;
            this.BListFiles.Click += new System.EventHandler(this.BListFiles_Click);
            // 
            // CHBDoNotClose
            // 
            this.CHBDoNotClose.AutoSize = true;
            this.CHBDoNotClose.Location = new System.Drawing.Point(16, 131);
            this.CHBDoNotClose.Name = "CHBDoNotClose";
            this.CHBDoNotClose.Size = new System.Drawing.Size(145, 17);
            this.CHBDoNotClose.TabIndex = 13;
            this.CHBDoNotClose.Text = "Do not close after upload";
            this.CHBDoNotClose.UseVisualStyleBackColor = true;
            // 
            // label6
            // 
            this.label6.AutoSize = true;
            this.label6.Location = new System.Drawing.Point(13, 151);
            this.label6.Name = "label6";
            this.label6.Size = new System.Drawing.Size(152, 13);
            this.label6.TabIndex = 15;
            this.label6.Text = "Choose which article to upload";
            // 
            // CBChoose
            // 
            this.CBChoose.DropDownStyle = System.Windows.Forms.ComboBoxStyle.DropDownList;
            this.CBChoose.FormattingEnabled = true;
            this.CBChoose.Location = new System.Drawing.Point(12, 171);
            this.CBChoose.Name = "CBChoose";
            this.CBChoose.Size = new System.Drawing.Size(201, 21);
            this.CBChoose.TabIndex = 14;
            this.CBChoose.SelectedIndexChanged += new System.EventHandler(this.CBChoose_SelectedIndexChanged);
            // 
            // SEWEBARForm
            // 
            this.AcceptButton = this.BPublish;
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.CancelButton = this.BClose;
            this.ClientSize = new System.Drawing.Size(641, 233);
            this.Controls.Add(this.label6);
            this.Controls.Add(this.CBChoose);
            this.Controls.Add(this.CHBDoNotClose);
            this.Controls.Add(this.BListFiles);
            this.Controls.Add(this.BClose);
            this.Controls.Add(this.BPublish);
            this.Controls.Add(this.TBNewArticle);
            this.Controls.Add(this.label5);
            this.Controls.Add(this.label4);
            this.Controls.Add(this.LVArticles);
            this.Controls.Add(this.TBPassword);
            this.Controls.Add(this.label3);
            this.Controls.Add(this.label2);
            this.Controls.Add(this.TBUserName);
            this.Controls.Add(this.CBXMLRPCHost);
            this.Controls.Add(this.label1);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.MaximizeBox = false;
            this.MinimumSize = new System.Drawing.Size(657, 239);
            this.Name = "SEWEBARForm";
            this.Text = "SEWEBAR Publisher";
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion

        private System.Windows.Forms.Label label1;
        private System.Windows.Forms.ComboBox CBXMLRPCHost;
        private System.Windows.Forms.TextBox TBUserName;
        private System.Windows.Forms.Label label2;
        private System.Windows.Forms.Label label3;
        private System.Windows.Forms.TextBox TBPassword;
        private System.Windows.Forms.ListView LVArticles;
        private System.Windows.Forms.Label label4;
        private System.Windows.Forms.Label label5;
        private System.Windows.Forms.TextBox TBNewArticle;
        private System.Windows.Forms.Button BPublish;
        private System.Windows.Forms.Button BClose;
        private System.Windows.Forms.Button BListFiles;
        private System.Windows.Forms.CheckBox CHBDoNotClose;
        private System.Windows.Forms.Label label6;
        private System.Windows.Forms.ComboBox CBChoose;
    }
}

