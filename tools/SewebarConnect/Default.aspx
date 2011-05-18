<%@ Page Language="C#" MasterPageFile="~/Master.Master" AutoEventWireup="true" CodeBehind="Default.aspx.cs" Inherits="SewebarWeb._Default" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>

<asp:Content ID="Content2" ContentPlaceHolderID="ContentPlaceHolder1" runat="server">
    <div>
	<h1>SEWEBAR Connect</h1>
    <ol>
		<li>Create copy of empty Metabase</li>
		<li>Import DataDictionary</li>
		<li>Import task</li>
		<li>Run task</li>
		<li>Get results</li>
	</ol>
	<%=System.AppDomain.CurrentDomain.BaseDirectory %>
    </div>
</asp:Content>