package de.qterra.gnd.util;

import org.apache.log4j.Logger;

import javax.xml.bind.annotation.XmlRootElement;

@XmlRootElement
public class PersonResult {

	public PersonResult(){
		
	}


	private String firstName = null;
	private String lastName = null;
	private String preferredName = null;
	private String name = null;
	private String academicTitle = null;
	private String birth = null;
	private String biogr = null;
	private String persIdent = null;
	private String persIdentUri = null;
	private String wpUrl = null;

	public String getFirstName() {
		return firstName;
	}
	public void setFirstName(String firstName) {
		this.firstName = firstName;
	}
	public String getLastName() {
		return lastName;
	}
	public void setLastName(String lastName) {
		this.lastName = lastName;
	}
	public String getAcademicTitle() {
		return academicTitle;
	}
	public void setAcademicTitle(String academicTitle) {
		this.academicTitle = academicTitle;
	}
	public String getBirth() {
		return birth;
	}
	public void setBirth(String birth) {
		this.birth = birth;
	}
	public String getBiogr() {
		return biogr;
	}
	public void setBiogr(String biogr) {
		this.biogr = biogr;
	}
	public String getPersIdent() {
		return persIdent;
	}
	public void setPersIdent(String persIdent) {
		this.persIdent = persIdent;
	}
	public String getPersIdentUri() {
		return persIdentUri;
	}
	public void setPersIdentUri(String persIdentUri) {
		this.persIdentUri = persIdentUri;
	}
	public String getWpUrl() {
		return wpUrl;
	}
	public void setWpUrl(String wpUrl) {
		this.wpUrl = wpUrl;
	}

	public String getName() {
		if(firstName != null && lastName != null){
			name = lastName + "," + firstName;
		}
		return name;
	}
	public String getPrefferedName() {
		return preferredName;
	}
	public void setPrefferedName(String prefferedName) {
		this.preferredName = prefferedName;
	}


}
