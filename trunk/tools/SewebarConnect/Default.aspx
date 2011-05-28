<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Default.aspx.cs" Inherits="SewebarWeb._Default" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <title>SEWEBAR Connect</title>
</head>
<body>
    <form id="form1" runat="server">
    <div>
	<h1>SEWEBAR Connect</h1>
    <ol>
		<li>
			New user session
			<ul>
				<li>Create copy of empty Metabase</li>
			</ul>
		</li>
		<li><a href="Import.ashx">Import DataDictionary</a></li>
		<li>
			<a href="Task.ashx">Run task</a>
			<ul>
				<li>Import task</li>
				<li>Generate results</li>
				<li>Response results</li>
			</ul>
		</li>
	</ol>
	<hr />
	<dl>
		<dt>BaseDirectory</dt>
		<dd><%=System.AppDomain.CurrentDomain.BaseDirectory %></dd>
		<dt>Session ID</dt>
		<dd><%=Session.SessionID %></dd>
	</dl>
    </div>
	</form>
</body>
</html>