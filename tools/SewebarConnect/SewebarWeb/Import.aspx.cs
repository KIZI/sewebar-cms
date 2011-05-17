﻿using System;
using LMWrapper.LISpMiner;

namespace SewebarWeb
{
	public partial class Import : System.Web.UI.Page
	{
		protected void Page_Load(object sender, EventArgs e)
		{
			var importer = new LMSwbImporter
			{
				Environment = Global.Environment,
				Dsn = Session["metabaseDsn"].ToString(),
				Input = String.Format(@"{0}\xml\DataDictionary.pmml", AppDomain.CurrentDomain.BaseDirectory),
				Quiet = true
			};

			importer.Import();
		}
	}
}