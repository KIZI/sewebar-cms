package xquerysearch.domain.arbquery;

import java.util.Set;

/**
 * Domain object representing DBASetting element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class DbaSetting {

	private String id;
	private String type;
	private String match;
	private Set<String> baSettingRefs;
	private String literalSign;

	/**
	 * @return the id
	 */
	public String getId() {
		return id;
	}

	/**
	 * @param id
	 *            the id to set
	 */
	public void setId(String id) {
		this.id = id;
	}

	/**
	 * @return the type
	 */
	public String getType() {
		return type;
	}

	/**
	 * @param type
	 *            the type to set
	 */
	public void setType(String type) {
		this.type = type;
	}

	/**
	 * @return the match
	 */
	public String getMatch() {
		return match;
	}

	/**
	 * @param match
	 *            the match to set
	 */
	public void setMatch(String match) {
		this.match = match;
	}

	/**
	 * @return the baSettingRefs
	 */
	public Set<String> getBaSettingRefs() {
		return baSettingRefs;
	}

	/**
	 * @param baSettingRefs
	 *            the baSettingRefs to set
	 */
	public void setBaSettingRefs(Set<String> baSettingRefs) {
		this.baSettingRefs = baSettingRefs;
	}

	/**
	 * @return the literalSign
	 */
	public String getLiteralSign() {
		return literalSign;
	}

	/**
	 * @param literalSign
	 *            the literalSign to set
	 */
	public void setLiteralSign(String literalSign) {
		this.literalSign = literalSign;
	}

}
