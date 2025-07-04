import DashAbout from "../components/dash/DashSobre"
import Footer from "../components/Footer"
import Navbar from "../components/header/Navbar"

const About = () => {
  return (
    <div className="bg-myColor">
      <Navbar/>
      <DashAbout/>
      <Footer/>
    </div>
  )
}

export default About