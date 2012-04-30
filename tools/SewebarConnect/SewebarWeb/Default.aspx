<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Default.aspx.cs" Inherits="SewebarWeb._Default" %>
<%@ Import Namespace="LMWrapper.ODBC" %>
<%@ Import Namespace="SewebarWeb" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
	<title>SEWEBAR Connect</title>
	<link href="styles.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<form id="form1" runat="server">
	<div>
	<h1>SEWEBAR Connect</h1>
	<ol>
		<li>Create new metabase
			<ol class="a">
				<li>
					<a href="Register.ashx?type=mysqlconnection&amp;server=localhost&amp;database=barbora&amp;username=lisp&amp;password=lisp">Registration</a>
					<ul>
						<li>Input <em>(Only MySQL supported at this moment)</em>
							<dl>
								<dt>type</dt>
								<dd>{ AccessConnection | MySQLConnection }</dd>
				
								<dt>server</dt>
								<dd></dd>
				
								<dt>database</dt>
								<dd></dd>
				
								<dt>username</dt>
								<dd></dd>
				
								<dt>password</dt>
								<dd></dd>
							</dl>
						</li>
						<li>Output example: 
							<code>
							&lt;response id="{Registered LISpMiner ID}" status="success" /&gt;
							</code>
						</li>
					</ul>
				</li>
				<li>
					Session
					<ul><li>No registration needed. With every new user session new LISpMiner with default settings is created. Creates copy of empty Metabase.</li></ul>
				</li>
			</ol>
		</li>	
		<li>
			<a href="Import.ashx">Import DataDictionary</a>
			<ul>
				<li>Input
					<dl>
						<dt>guid</dt>
						<dd><em>Optional</em> Registered LISpMiner ID to use. If not provided session LISpMiner is used/created.</dd>
						<dt>content</dt>
						<dd>DataDictionary in format LISpMiner can import it.</dd>
					</dl>
				</li>
				<li>Output example:
					<code>
					&lt;response id="{used LISpMiner ID}" status="success" /&gt;
					</code>
				</li>
			</ul>
		</li>
		<li>
			<a href="Task.ashx">Run task</a>
			<ul>
				<li>Input
					<dl>
						<dt>guid</dt>
						<dd><em>Optional</em> Registered LISpMiner ID to use. If not provided session LISpMiner is used/created.</dd>
						<dt>content</dt>
						<dd>Task in format LISpMiner can import it.</dd>
					</dl>
				</li>
				<li>Output: Task results exported from LISpMiner</li>
			</ul>
		</li>
	</ol>
	<h2>Exceptions</h2>
	In case of failured execution the response contains in header status code <em>500 Internal Server Error</em> and body:
	<br/>
	<code>
	&lt;response status="failure"&gt;&lt;message&gt;{Exception message}&lt;/message&gt;&lt;/response&gt;
	</code>
	<h1>Environment</h1>
	<dl>
		<dt>BaseDirectory</dt>
		<dd><%=System.AppDomain.CurrentDomain.BaseDirectory %></dd>
		<dt>Session ID</dt>
		<dd><%=Session.SessionID %></dd>
	</dl>
	<% if(Global.Environment.ExistingMiners.Count > 0) { %>
	<h2>Existing miners</h2>
	<ol>
		<% foreach (var miner in Global.Environment.ExistingMiners) { %>
		<li><%=miner %></li>
		<% } %>
	</ol>
	<% } %>
	<% if (Global.ExistingSessionMiners.Count > 0) { %>
	<h2>Active session miners</h2>
	<ol>
		<% foreach (var miner in Global.ExistingSessionMiners) { %>
		<li><%=miner %></li>
		<% } %>
	</ol>
	<% } %>

	<% if (ODBCManagerRegistry.GetAllDataSourceNames().Count > 0) { %>
	<h2>Active session miners</h2>
	<ol>
		<% foreach (var dsn in ODBCManagerRegistry.GetAllDataSourceNames().Keys)
	 { %>
		<li><%=dsn %></li>
		<% } %>
	</ol>
	<% } %>
	</div>
	</form>
</body>
</html>