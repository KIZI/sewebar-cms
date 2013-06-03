package izi_repository.domain;

import java.util.Set;

/**
 * Domain object representing Derived Boolean Attribute (DBA).
 * 
 * @author Tomas Marek
 * 
 */
public class DerivedBooleanAttribute {

	private String id;
	private ConnectiveType connective;
	private String parentId;
	private Set<String> childIds;

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
	 * @return the connective
	 */
	public ConnectiveType getConnective() {
		return connective;
	}

	/**
	 * @param connective
	 *            the connective to set
	 */
	public void setConnective(ConnectiveType connective) {
		this.connective = connective;
	}

	/**
	 * @return the parentId
	 */
	public String getParentId() {
		return parentId;
	}

	/**
	 * @param parentId
	 *            the parentId to set
	 */
	public void setParentId(String parentId) {
		this.parentId = parentId;
	}

	/**
	 * @return the childIds
	 */
	public Set<String> getChildIds() {
		return childIds;
	}

	/**
	 * @param childIds
	 *            the childIds to set
	 */
	public void setChildIds(Set<String> childIds) {
		this.childIds = childIds;
	}

}
