package xquerysearch.domain;

/**
 * Object (Enum) representing connective type for {@link DerivedBooleanAttribute}.
 * 
 * @author Tomas Marek
 *
 */
public enum ConnectiveType {

	CONJUNCTION("conjunction"), DISJUNCTION("disjunction") ;
	
	private String name;

	private ConnectiveType(String name) {
		this.name = name;
	}
	
	/**
	 * @return the name
	 */
	public String getName() {
		return name;
	}
}
