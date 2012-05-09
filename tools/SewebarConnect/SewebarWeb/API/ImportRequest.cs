﻿using System;
using System.IO;
using System.Web;
using LMWrapper.LISpMiner;

namespace SewebarWeb.API
{
    public class ImportRequest : Request
    {
        private string _dataDictionaryPath;

        public string DataDictionary
        {
            get
            {
                return this.HttpContext.Request["content"];
            }
        }

        public string DataDictionaryPath
        {
            get
            {
                if (String.IsNullOrEmpty(this._dataDictionaryPath))
                {
                    this._dataDictionaryPath = String.Format(@"{0}/DataDictionary_{1:yyyyMMdd-Hmmss}.xml",
                                                             this.DataFolder,
                                                             DateTime.Now);
                }

                if (!File.Exists(this._dataDictionaryPath))
                {
                    using (var file = File.CreateText(this._dataDictionaryPath))
                    {
                        file.Write(this.DataDictionary);
                        file.Close();
                    }
                }

                return this._dataDictionaryPath;
            }
        }

        public ImportRequest(LISpMiner miner, HttpContext context) : base(miner, context)
        {
        }
    }
}