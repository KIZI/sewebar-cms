package xquery_servlet;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlManager;
import com.sleepycat.dbxml.XmlQueryContext;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlTransaction;
import com.sleepycat.dbxml.XmlValue;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileReader;

/**
 * Trida pro ovladani a komunikaci s Berkeley XML DB
 * @author Tomas
 */
public class BDBXMLHandler {

    public BDBXMLHandler() {
    }

    /**
     * Metoda pro vymazani dokumentu z XML DB
     * @param id ID dokumentu v DB
     * @param mgr XmlManager
     * @return Zprava - splneno/chyba
     */
    public String removeDocument (String id, XmlManager mgr, String containerName){
        String output = "";
        try {

            XmlContainer cont = mgr.openContainer(containerName);
            XmlTransaction txn = mgr.createTransaction();

            if (cont.getDocument(id) != null) {
                cont.deleteDocument(id);
                output += "<message>Dokument " + id + " smazan!</message>";
            } else {
                output += "<error>Dokument nenalezen!</error>";
            }

            txn.commit();

            cleanup(cont);

        } catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }

        return output;
    }

    /**
     * Metoda pro zobrazeni dokumentu z XML DB
     * @param id ID dokumentu v DB
     * @param mgr XmlManager
     * @return Zobrazeni dokumentu/chyba
     */
    public String getDocument(String id, XmlManager mgr, String containerName){
        String output = "";
        try {
            XmlContainer cont = mgr.openContainer(containerName);
            XmlTransaction txn = mgr.createTransaction();

            if (cont.getDocument(id) != null) {
                XmlDocument doc = cont.getDocument(id);
                output += doc.getContentAsString();
            } else {
                output += "<error>Dokument nenalezen!</error>";
            }
            txn.commit();

            cleanup(cont);

        } catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    
    public String query_10(String search, XmlManager mgr, QueryHandler qh, String containerName, String queryDir) {
        String output = "";
        String output_temp = "";
        for (int i=0; i<10; i++){
            output += "<pokus cislo=\""+ i +"\">";
            double time_start = System.currentTimeMillis();
            output_temp = query("", search, 0, mgr, qh, containerName, queryDir)[1];
            output += "<time>"+ (System.currentTimeMillis() - time_start) +"</time>";
            if (i == 9){
                output += output_temp;
            }
            output += "</pokus>";
        }
        return output;
    }

   /**
     * Metoda slouzici k vyhledavani v BDB XML pomoci XQuery
     * Moznosti zadani - prima XQuery/pouziti ulozene XQuery a pridani vstupniho dotazu
     * @param id ID ulozene XQuery
     * @param search Vstupni dotaz pro XQuery
     * @param typ Typ pouzite XQuery - 0 pro primou, 1 pro ulozenou
    */
    public String[] query(String id, String search, int typ, XmlManager mgr, QueryHandler qh, String containerName, String queryDir){
        String output[] = new String[2];
        output[1] = "";
        int chyba = 0;
        double cas_zacatek = System.currentTimeMillis();

        try {
            XmlContainer cont = mgr.openContainer(containerName);
            String query = "";
            if (typ == 0) {
                query = search;
            } else {
                    if (qh.getQuery(id, queryDir)[0].toString().equals("1")) {
                        output[1] = qh.getQuery(id, queryDir)[1].toString();
                        chyba = 1;
                    } else {
                        query = qh.getQuery(id, queryDir)[1].toString();
                        query += "\nlet $zadani := " + search
                                + "\nreturn" +
                                        "\nlocal:mainFunction($zadani)";
                    }
            }
            if (chyba != 1) {
            XmlQueryContext qc = mgr.createQueryContext();
            //qc.setVariableValue("zadani", search);

            XmlTransaction txn = mgr.createTransaction();

            //XmlQueryExpression expr = mgr.prepare(txn, query, qc);
            XmlResults res = mgr.query(query, qc);

            if (res != null) {
            // Process results -- just print them
                    XmlValue value = new XmlValue();
                    while ((value = res.next()) != null) {
                        output[1] += (value.asString());
                    }
            } else {
                output[1] = "<error>Zadny vysledek</error>";
            }
            txn.commit();

         res.delete();

            cleanup(cont);
            }
        } catch (XmlException e) {
                output[1] += "<error>"+e.toString()+"</error>";
        } catch (Throwable e) {
                output[1] += "<error>"+e.toString()+"</error>";
        }
                double cas_konec = System.currentTimeMillis();
                output[0] = "" + ((cas_konec - cas_zacatek)/1000);
        return output;
    }

    /**
     * Metoda pro vlozeni vice dokumentu najednou,
     * dokumenty rozdeleny sekvenci znaku ;;;NEXTPMML;;;
     * @param docs Vsechny tela vkladanych dokumentu oddelene danou sekvenci znaku
     * @param names Vsechny ID vkladanych dokumentu oddelene danou sekvenci znaku
     * @param mgr XmlManager
     * @return Zprava pro kazdy dokument - vlozeno/chyba
     */

    /*public String[] moreDocuments(String docs, String names, XmlManager mgr){
    String output[] = new String[2];
        output[1] = "";
        long cas_zacatek = System.currentTimeMillis();
        long cas_konec = 0;
        String[] dokumenty = docs.split(";;;NEXTPMML;;;");
        String[] jmena = names.split(";;;NEXTPMML;;;");
        if (dokumenty.length != jmena.length){
                output[1] = "<error>Nastala chyba!!!</error>";
        } else {
                for (int i = 0; i < dokumenty.length; i++){
                    output[1] += "\n" + indexDocument(dokumenty[i], jmena[i], mgr);
                }
        cas_konec = System.currentTimeMillis();
        output[0] = "" + ((cas_konec - cas_zacatek)/1000);
        }
        return output;
    }*/

    /**
     * Metoda pro ziskani ID dokumentu ulozenych v XML DB
     * @param mgr XmlManager
     * @return ID vsech dokumentu v XML DB
     */
    public String getDocsNames(XmlManager mgr, String containerName){
        String output = "";

        try {
            /*
                    <docs><count>{count(for $a in collection(\""+containerName+"\")
                    return $a)}</count>
                    <names>{for $a in collection(\""+containerName+"\")
                    return <doc>{dbxml:metadata(\"dbxml:name\", $a)}</doc>}</names>
                    </docs>
             */
            //for $a in collection(\""+containerName+"\") return dbxml:metadata(\"dbxml:name\", $a)
            String query = "for $a in collection(\""+containerName+"\")"
                    + "order by dbxml:metadata(\"dbxml:name\", $a)"
                    + "return dbxml:metadata(\"dbxml:name\", $a)";

            XmlContainer cont = mgr.openContainer(containerName);

            XmlQueryContext qc = mgr.createQueryContext();

            XmlTransaction txn = mgr.createTransaction();

            XmlResults res = mgr.query(query, qc);

            XmlValue value = new XmlValue();
            while ((value = res.next()) != null) {
                output += ("<doc>" + value.asString() + "</doc>");
            }

            txn.commit();

            output += cleanup(cont);

        } catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    /**
     * Metoda pro vlozeni dokumentu do XML DB
     * @param document Telo dokumentu (String)
     * @param id ID dokumentu
     * @return Zprava - ulozeno/chyba
     */
    public String indexDocument(String document, String id, XmlManager mgr, String containerName, String useTransformation, String xsltPath){

        String output = "";
        String xml_doc = "";
        long act_time_long = System.currentTimeMillis();
        String act_time = Long.toString(act_time_long);
        try {
            if (useTransformation.equals("true")) {
            /*File xmlFile = new File(tempDir + act_time +".xml");
            File xsltFile = new File(xsltPath);

            XSLTTransformer xslt = new XSLTTransformer();
            FileOutputStream fos = new FileOutputStream(xmlFile);
                    OutputStreamWriter osw = new OutputStreamWriter(fos, "UTF-8");
                    osw.write(document);
                    osw.close();*/

            File xsltFile = new File(xsltPath);
            XSLTTransformer xslt = new XSLTTransformer();
            xml_doc = xslt.XSLT_transformation(document, xsltFile);
            //output += "<xslt>" + xslt_output + "</xslt>";
            //xmlFile.delete();
            /*File xml_to_save = new File(xslt_output);

            FileReader fr = new FileReader(xml_to_save);
            BufferedReader br = new BufferedReader(fr);
            String radek = br.readLine();
            while (radek != null) {
                    xml_doc += radek + "\n";
                    radek = br.readLine();
            }
            br.close();
            fr.close();
            xml_to_save.delete();*/
        } else {
                xml_doc = document;
        }

            XmlContainer cont = mgr.openContainer(containerName);

            XmlTransaction txn = mgr.createTransaction();

            cont.putDocument(id, xml_doc);
            output += "<message>Dokument " + id + " vlozen</message>";

            txn.commit();

            cleanup(cont);

        } catch (XmlException e) {
                output += "<error>"+e.toString()+"</error>";
        } catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    /**
     * Metoda pro vlozeni dokumentu do XML DB
     * @param document Telo dokumentu (File)
     * @param id ID dokumentu
     * @return Zprava - ulozeno/chyba
     */
    public String indexDocument(File document, String id, XmlManager mgr, String containerName, String useTransformation, String xsltPath){
        String xml_doc = "";
        String output = "";
        long act_time_long = System.currentTimeMillis();
        //String act_time = Long.toString(act_time_long);
        try {
            if (useTransformation.equals("true")) {
            File xsltFile = new File(xsltPath);

            XSLTTransformer xslt = new XSLTTransformer();

            xml_doc = xslt.XSLT_transformation(document, xsltFile);
            output += "<xslt_time>" + (System.currentTimeMillis() - act_time_long) + "</xslt_time>";
            //output += "<xslt>" + xslt_output + "</xslt>";
            //xmlFile.delete();
            /*File xml_to_save = new File(xslt_output);

            FileReader fr = new FileReader(xml_to_save);
            BufferedReader br = new BufferedReader(fr);
            String radek = br.readLine();
            while (radek != null) {
                    xml_doc += radek + "\n";
                    radek = br.readLine();
            }
            br.close();
            fr.close();
            xml_to_save.delete();*/
        } else {
            FileReader rdr = null;
            BufferedReader out = null;
            rdr = new FileReader(document);
            out = new BufferedReader(rdr);
            String radek = out.readLine();
            while (radek != null){
                xml_doc += radek + "\n";
                radek = out.readLine();
            }

        }

            XmlContainer cont = mgr.openContainer(containerName);

            XmlTransaction txn = mgr.createTransaction();

            cont.putDocument(id, xml_doc);
            output += "<message>Dokument " + id + " vlozen</message>";
            output += "<doc_time>" + (System.currentTimeMillis() - act_time_long) + "</doc_time>";
            txn.commit();

            cleanup(cont);

        } catch (XmlException e) {
                output += "<error>"+e.toString()+"</error>";
        } catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }

        return output;
    }

    /**
     * Metoda pro nahrani vice dokumentu ze slozky
     * @param folder slozka, ze ktere se maji soubory nahrat
     * @param mgr
     * @param containerName
     * @param useTransformation
     * @param xsltPath
     * @return
     */
    public String indexDocumentMultiple (String folder, XmlManager mgr, String containerName, String useTransformation, String xsltPath) {
        String output = "";
        File uploadFolder = new File(folder);
        File uploadFiles[] = uploadFolder.listFiles();


        for(int i = 0; i < uploadFiles.length; i++){
            output += indexDocument(uploadFiles[i], uploadFiles[i].getName(), mgr, containerName, useTransformation, xsltPath);
        }
        //output += "AHOJ";
        return output;
    }



    /**
     * Metoda pro pridani indexu XML DB
     * @param index Zadani indexu
     * @param mgr XmlManager
     * @return
     */
    public String addIndex(String index, XmlManager mgr, String containerName) {
        String output = "";

        try {
            XmlContainer cont = mgr.openContainer(containerName);

            XmlTransaction txn = mgr.createTransaction();
            String[] index_pole = index.split(" ");
            if (index_pole.length == 3) {
            cont.addIndex(index_pole[0], index_pole[1], index_pole[2]);
            output = "<message>Index " + index + " pridan</message>";

            txn.commit();
            } else {
                output = "<error>Spatne zadany index</error>";
            }

            cleanup(cont);

        } catch (XmlException e) {
                output += "<error>"+e.toString()+"</error>";
        }catch (Throwable e) {
                output += "<error>"+e.toString()+"</error>";
        }
        return output;
    }

    /**
     * Metoda pro uklizeni spojeni - container
     * @param cont Pouzity container
     * @return
     */
    private static String cleanup(XmlContainer cont) {
    try {
        if (cont != null) {
            cont.delete();
        }
    } catch (Exception e) {
            }
    return "";//output+"</cleanup_err>";
    }
}
