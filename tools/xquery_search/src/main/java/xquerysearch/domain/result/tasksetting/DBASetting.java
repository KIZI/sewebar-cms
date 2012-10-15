package xquerysearch.domain.result.tasksetting;

import java.util.Set;

/**
 * Domain object representing DBASetting element from TaskSetting from query
 * result.
 * 
 * @author Tomas Marek
 * 
 */
public class DBASetting {

	private String id;
	private String type;
	private String name;
	private Set<String> baSettingRefs;
	private Integer minimalLength;
	private Integer maximalLength;
	private String literalSign;
	private String literalType;
	private String equivalenceClass;
	private Set<DBASetting> dbaSettings;
	private Set<BBASetting> bbaSettings;

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
	 * @return the name
	 */
	public String getName() {
		return name;
	}

	/**
	 * @param name
	 *            the name to set
	 */
	public void setName(String name) {
		this.name = name;
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
	 * @return the minimalLength
	 */
	public Integer getMinimalLength() {
		return minimalLength;
	}

	/**
	 * @param minimalLength
	 *            the minimalLength to set
	 */
	public void setMinimalLength(Integer minimalLength) {
		this.minimalLength = minimalLength;
	}

	/**
	 * @return the maximalLength
	 */
	public Integer getMaximalLength() {
		return maximalLength;
	}

	/**
	 * @param maximalLength
	 *            the maximalLength to set
	 */
	public void setMaximalLength(Integer maximalLength) {
		this.maximalLength = maximalLength;
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

	/**
	 * @return the literalType
	 */
	public String getLiteralType() {
		return literalType;
	}

	/**
	 * @param literalType
	 *            the literalType to set
	 */
	public void setLiteralType(String literalType) {
		this.literalType = literalType;
	}

	/**
	 * @return the equivalenceClass
	 */
	public String getEquivalenceClass() {
		return equivalenceClass;
	}

	/**
	 * @param equivalenceClass
	 *            the equivalenceClass to set
	 */
	public void setEquivalenceClass(String equivalenceClass) {
		this.equivalenceClass = equivalenceClass;
	}

	/**
	 * @return the dbaSettings
	 */
	public Set<DBASetting> getDbaSettings() {
		return dbaSettings;
	}

	/**
	 * @param dbaSettings
	 *            the dbaSettings to set
	 */
	public void setDbaSettings(Set<DBASetting> dbaSettings) {
		this.dbaSettings = dbaSettings;
	}

	/**
	 * @return the bbaSettings
	 */
	public Set<BBASetting> getBbaSettings() {
		return bbaSettings;
	}

	/**
	 * @param bbaSettings
	 *            the bbaSettings to set
	 */
	public void setBbaSettings(Set<BBASetting> bbaSettings) {
		this.bbaSettings = bbaSettings;
	}

	/**
	 * @{inheritDoc
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<DBASetting id=\"" + id + "\" type=\"" + type + "\">");
		ret.append("<Name>" + name + "</Name>");
		for (String baSettingRef : baSettingRefs) {
			ret.append("<BASettingRef>" + baSettingRef + "</BASettingRef>");
		}
		if (minimalLength != null) {
			ret.append("<MinimalLength>" + minimalLength + "</MinimalLength>");
		}
		if (maximalLength != null) {
			ret.append("<MaximalLength>" + maximalLength + "</MaximalLength>");
		}
		if (literalSign != null) {
			ret.append("<LiteralSign>" + literalSign + "</LiteralSign>");
		}
		if (literalType != null) {
			ret.append("<LiteralType>" + literalType + "</LiteralType>");
		}
		if (equivalenceClass != null) {
			ret.append("<EquivalenceClass>" + equivalenceClass + "</EquivalenceClass>");
		}
		if (dbaSettings != null) {
			for (DBASetting dbaSetting : dbaSettings) {
				ret.append(dbaSetting.toString());
			}
		}
		if (bbaSettings != null) {
			for (BBASetting bbaSetting : bbaSettings) {
				ret.append(bbaSetting.toString());
			}
		}
		ret.append("</DBASetting>");
		return ret.toString();
	}
}
