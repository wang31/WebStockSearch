import java.io.*;
import java.net.*;
import javax.servlet.*;
import javax.servlet.http.*;
import java.util.List;
import org.jdom.Document;
import org.jdom.Element;
import org.jdom.JDOMException;
import org.jdom.input.SAXBuilder;
import org.json.JSONArray;
import org.json.JSONObject;

public class HW8Servlet extends HttpServlet {
	public void doGet(HttpServletRequest request, HttpServletResponse response) throws IOException, ServletException{
		try{
			String symbol = request.getParameter("symbol");
			String urlString = "http://default-environment-6efmanduzp.elasticbeanstalk.com/?symbol=" + symbol;
			URL url = new URL(urlString);
			URLConnection con = url.openConnection();
			con.setAllowUserInteraction(false);
			String result;
			InputStream is = url.openStream();
			BufferedReader br = new BufferedReader(new InputStreamReader(is));
			StringBuffer buffer = new StringBuffer();
			String line;
			while((line = br.readLine()) != null){
				buffer.append(line);
			}
			result = buffer.toString();
			br.close();

			//parse the string to xml object
			SAXBuilder builder = new SAXBuilder();
			Reader reader = new StringReader(result);
			Document doc = builder.build(reader);
			Element root = doc.getRootElement();

			//construct json from the xml dom
			Element xml_quote = root.getChild("Quote");
			JSONObject json_ret = new JSONObject();
			JSONObject json_quote = new JSONObject();
			json_quote.put("ChangeType", xml_quote.getChild("ChangeType").getText());
			json_quote.put("Change", xml_quote.getChild("Change").getText());
			json_quote.put("ChangeInPercent", xml_quote.getChild("ChangeInPercent").getText());
			json_quote.put("LastTradePriceOnly", xml_quote.getChild("LastTradePriceOnly").getText());
			json_quote.put("Open", xml_quote.getChild("Open").getText());
			json_quote.put("YearLow", xml_quote.getChild("YearLow").getText());
			json_quote.put("YearHigh", xml_quote.getChild("YearHigh").getText());
			json_quote.put("Volume", xml_quote.getChild("Volume").getText());
			json_quote.put("OneYearTargetPrice", xml_quote.getChild("OneYearTargetPrice").getText());
			json_quote.put("Bid", xml_quote.getChild("Bid").getText());
			json_quote.put("DaysLow", xml_quote.getChild("DaysLow").getText());
			json_quote.put("DaysHigh", xml_quote.getChild("DaysHigh").getText());
			json_quote.put("Ask", xml_quote.getChild("Ask").getText());
			json_quote.put("AverageDailyVolume", xml_quote.getChild("AverageDailyVolume").getText());
			json_quote.put("PreviousClose", xml_quote.getChild("PreviousClose").getText());
			json_quote.put("MarketCapitalization", xml_quote.getChild("MarketCapitalization").getText());

			JSONArray json_item = new JSONArray();
			List items = root.getChild("News").getChildren("Item");
			for(int i = 0; i < items.size(); i++){
				JSONObject entry = new JSONObject();
				Element node = (Element)items.get(i);
				entry.put("Link", node.getChildText("Link"));
				entry.put("Title", node.getChildText("Title"));
				json_item.put(entry);
			}

			JSONObject json_result = new JSONObject();
			json_result.put("Name", root.getChildText("Name"));
			json_result.put("Symbol", root.getChildText("Symbol"));
			json_result.put("Quote", json_quote);
			JSONObject json_news = new JSONObject();
			json_news.put("Item", json_item);
			json_result.put("News", json_news);
			json_result.put("StockChartImageURL", root.getChildText("StockChartImageURL"));

			json_ret.put("result", json_result);
			
			//return back json
			response.setContentType("application/json; charset=UTF-8");
			PrintWriter out = response.getWriter();
			out.print(json_ret);
			out.flush();
		}
		catch(MalformedURLException e){
			e.printStackTrace();
		}
		catch(IOException e){
			e.printStackTrace();
		}
		catch(JDOMException e){
			e.printStackTrace();
		}

	}
}